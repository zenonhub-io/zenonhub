<?php

declare(strict_types=1);

namespace App\Domains\Nom\Models;

use App\Domains\Nom\Enums\AcceleratorPhaseStatusEnum;
use App\Domains\Nom\Services\ZenonSdk;
use App\Models\Markable\Favorite;
use App\Traits\ModelCacheKeyTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Spatie\Sitemap\Contracts\Sitemapable;
use Spatie\Sitemap\Tags\Url;
use Throwable;

class AcceleratorPhase extends Model implements Sitemapable
{
    use AcceleratorFundingTrait, AcceleratorVotesTrait, HasFactory, ModelCacheKeyTrait;

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'nom_accelerator_phases';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'project_id',
        'hash',
        'name',
        'slug',
        'url',
        'description',
        'status',
        'znn_requested',
        'qsr_requested',
        'znn_price',
        'qsr_price',
        'created_at',
    ];

    protected static array $marks = [
        Favorite::class,
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'status' => AcceleratorPhaseStatusEnum::class,
        ];
    }

    //
    // Config

    public function toSitemapTag(): Url|string|array
    {
        return route('az.phase', ['hash' => $this->hash]);
    }

    //
    // Relations

    public function project(): BelongsTo
    {
        return $this->belongsTo(AcceleratorProject::class);
    }

    public function votes(): MorphMany
    {
        return $this->morphMany(Vote::class, 'votable');
    }

    //
    // Scopes

    public function scopeWhereOpen($query)
    {
        return $query->where('status', AcceleratorPhaseStatusEnum::OPEN->value);
    }

    public function scopeWherePaid($query)
    {
        return $query->where('status', AcceleratorPhaseStatusEnum::PAID->value);
    }

    public function scopeShouldSendVotingReminder($query)
    {
        $query
            ->whereTime('created_at', '>=', now()->subHour()->startOfHour()->format('H:i:s'))
            ->whereTime('created_at', '<', now()->subHour()->endOfHour()->format('H:i:s'))
            ->where(function ($q) {
                $q->whereDate('created_at', now()->subWeek())
                    ->orWhereDate('created_at', now()->subWeeks(2))
                    ->orWhereDate('created_at', now()->subWeeks(3))
                    ->orWhereDate('created_at', now()->subWeeks(4))
                    ->orWhereDate('created_at', now()->subWeeks(5))
                    ->orWhereDate('created_at', now()->subWeeks(6))
                    ->orWhereDate('created_at', now()->subWeeks(7))
                    ->orWhereDate('created_at', now()->subDays(8));
            });
    }

    //
    // Attributes

    public function getQuorumStatusAttribute(): string
    {
        if (! $this->is_quorum_reached) {
            $votesText = Str::plural('vote', $this->total_more_votes_needed);

            return "{$this->total_more_votes_needed} {$votesText} needed";
        }

        return 'Quorum reached';
    }

    public function getPhaseNumberAttribute(): int
    {
        return $this->project->phases->pluck('id')->sort()->search($this->id) + 1;
    }

    public function getRawJsonAttribute(): array
    {
        $cacheKey = $this->cacheKey('rawJson');
        $data = Cache::get($cacheKey);

        try {
            $newData = app(ZenonSdk::class)->getPhaseById($this->hash);
            Cache::forever($cacheKey, $newData);
            $data = $newData;
        } catch (Throwable $throwable) {
            // If API request fails, we do not need to do anything,
            // we will return previously cached data (retrieved at the start of the function).
        }

        return $data;
    }

    public function getIsFavouritedAttribute(): bool
    {
        if ($user = auth()->user()) {
            return Favorite::findExisting($this, $user);
        }

        return false;
    }
}
