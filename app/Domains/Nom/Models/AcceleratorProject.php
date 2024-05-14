<?php

declare(strict_types=1);

namespace App\Domains\Nom\Models;

use App\Domains\Nom\Enums\AcceleratorPhaseStatusEnum;
use App\Domains\Nom\Enums\AcceleratorProjectStatusEnum;
use App\Models\Markable\Favorite;
use App\Services\ZenonSdk;
use App\Traits\FindByColumnTrait;
use App\Traits\ModelCacheKeyTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Spatie\Sitemap\Contracts\Sitemapable;
use Spatie\Sitemap\Tags\Url;
use Throwable;

class AcceleratorProject extends Model implements Sitemapable
{
    use AcceleratorFundingTrait, AcceleratorVotesTrait, FindByColumnTrait, HasFactory, ModelCacheKeyTrait;

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
    protected $table = 'nom_accelerator_projects';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'chain_id',
        'owner_id',
        'hash',
        'name',
        'slug',
        'url',
        'description',
        'status',
        'znn_requested',
        'qsr_requested',
        'znn_remaining',
        'qsr_remaining',
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
            'modified_at' => 'datetime',
            'status' => AcceleratorProjectStatusEnum::class,
        ];
    }

    //
    // Config

    public function toSitemapTag(): Url|string|array
    {
        return route('az.project', ['hash' => $this->hash]);
    }

    //
    // Relations

    public function chain(): BelongsTo
    {
        return $this->belongsTo(Chain::class);
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    public function phases(): HasMany
    {
        return $this->hasMany(AcceleratorPhase::class, 'project_id');
    }

    public function latestPhase(): HasOne
    {
        return $this->hasOne(AcceleratorPhase::class)->latestOfMany();
    }

    public function votes(): MorphMany
    {
        return $this->morphMany(AcceleratorVote::class, 'votable');
    }

    //
    // Scopes

    public function scopeIsNew($query)
    {
        return $query->where('status', AcceleratorProjectStatusEnum::NEW->value);
    }

    public function scopeIsAccepted($query)
    {
        return $query->where('status', AcceleratorProjectStatusEnum::ACCEPTED->value);
    }

    public function scopeIsRejected($query)
    {
        return $query->where('status', AcceleratorProjectStatusEnum::REJECTED->value);
    }

    public function scopeIsNotRejected($query)
    {
        return $query->where('status', '!=', AcceleratorProjectStatusEnum::REJECTED->value);
    }

    public function scopeIsCompleted($query)
    {
        return $query->where('status', AcceleratorProjectStatusEnum::COMPLETE->value);
    }

    public function scopeIsOpen($query)
    {
        return $query->where('status', AcceleratorProjectStatusEnum::NEW->value)
            ->orWhere(function ($q) {
                $q->where('status', AcceleratorProjectStatusEnum::ACCEPTED->value)
                    ->whereHas('phases', function ($q2) {
                        $q2->where('status', AcceleratorPhaseStatusEnum::OPEN->value);
                    });
            });
    }

    public function scopeAwaitingPhases($query)
    {
        return $query->where('status', AcceleratorProjectStatusEnum::ACCEPTED->value)
            ->doesntHave('phases');
    }

    public function scopeWhereListSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('name', 'LIKE', "%{$search}%")
                ->orWhere('description', 'LIKE', "%{$search}%")
                ->orWhere('url', 'LIKE', "%{$search}%")
                ->orWhere('hash', '=', $search);
        })->orWhereHas('phases', function ($q) use ($search) {
            $q->where('name', 'LIKE', "%{$search}%")
                ->orWhere('description', 'LIKE', "%{$search}%")
                ->orWhere('url', 'LIKE', "%{$search}%")
                ->orWhere('hash', '=', $search);
        });
    }

    public function scopeOrderByLatest($query)
    {
        return $query->orderByRaw('(status = 0) DESC')
            ->orderBy('modified_at', 'desc')
            ->orderBy('slug', 'desc');
    }

    public function scopeHasRemainingFunds($query)
    {
        return $query->where(function ($q) {
            $q->where('znn_remaining', '>', 0)
                ->orWhere('qsr_remaining', '>', 0);
        });
    }

    public function scopeShouldSendVotingReminder($query)
    {
        $query
            ->whereTime('created_at', '>=', now()->subHour()->startOfHour()->format('H:i:s'))
            ->whereTime('created_at', '<', now()->subHour()->endOfHour()->format('H:i:s'))
            ->where(function ($q) {
                $q->whereDate('created_at', now()->subDays(5))
                    ->orWhereDate('created_at', now()->subDays(10))
                    ->orWhereDate('created_at', now()->subDays(13));
            });
    }

    //
    // Attributes

    public function getQuorumStatusAttribute(): string
    {
        if ($this->status === AcceleratorProjectStatusEnum::NEW->value && $this->total_more_votes_needed > 0) {
            $votesText = Str::plural('vote', $this->total_more_votes_needed);

            return "{$this->total_more_votes_needed} {$votesText} needed in {$this->open_for_time}";
        }

        if ($this->total_more_votes_needed > 0) {
            return 'Quorum not reached';
        }

        return 'Quorum reached';
    }

    public function getRawJsonAttribute(): array
    {
        $updateCache = true;
        $cacheKey = "nom.acceleratorProject.rawJson.{$this->id}";

        try {
            $znn = App::make(ZenonSdk::class);
            $data = $znn->accelerator->getProjectById($this->hash)['data'];
        } catch (Throwable $throwable) {
            $updateCache = false;
            $data = Cache::get($cacheKey);
        }

        if ($updateCache) {
            Cache::forever($cacheKey, $data);
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
