<?php

declare(strict_types=1);

namespace App\Models\Nom;

use App\DataTransferObjects\Nom\AcceleratorProjectDTO;
use App\Enums\Nom\AcceleratorPhaseStatusEnum;
use App\Enums\Nom\AcceleratorProjectStatusEnum;
use App\Services\ZenonSdk\ZenonSdk;
use App\Traits\ModelCacheKeyTrait;
use Database\Factories\Nom\AcceleratorProjectFactory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Laravel\Scout\Searchable;
use Spatie\Sitemap\Contracts\Sitemapable;
use Spatie\Sitemap\Tags\Url;
use Throwable;

class AcceleratorProject extends Model implements Sitemapable
{
    use AcceleratorFundingTrait, AcceleratorVotesTrait, HasFactory, ModelCacheKeyTrait, Searchable;

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
            'status' => AcceleratorProjectStatusEnum::class,
        ];
    }

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory(): Factory
    {
        return AcceleratorProjectFactory::new();
    }

    //
    // Config

    public function toSitemapTag(): Url|string|array
    {
        return route('accelerator-z.project.detail', ['hash' => $this->hash]);
    }

    /**
     * {@inheritDoc}
     */
    public function toSearchableArray(): array
    {
        return [
            'name' => $this->name,
            'description' => $this->description,
        ];
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
        return $this->morphMany(Vote::class, 'votable');
    }

    //
    // Scopes

    public function scopeWhereNew($query)
    {
        return $query->where('status', AcceleratorProjectStatusEnum::NEW->value);
    }

    public function scopeWhereAccepted($query)
    {
        return $query->where('status', AcceleratorProjectStatusEnum::ACCEPTED->value);
    }

    public function scopeWhereRejected($query)
    {
        return $query->where('status', AcceleratorProjectStatusEnum::REJECTED->value);
    }

    public function scopeWhereNotRejected($query)
    {
        return $query->where('status', '!=', AcceleratorProjectStatusEnum::REJECTED->value);
    }

    public function scopeWhereCompleted($query)
    {
        return $query->where('status', AcceleratorProjectStatusEnum::COMPLETE->value);
    }

    public function scopeWhereOpen($query)
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
            ->orderBy('updated_at', 'desc')
            ->orderBy('slug', 'desc');
    }

    public function scopeHasRemainingFunds($query)
    {
        return $query->where(function ($q) {
            $q->where('znn_remaining', '>', 0)
                ->orWhere('qsr_remaining', '>', 0);
        });
    }

    //
    // Attributes

    public function getIsVotingOpenAttribute(?Carbon $dateTime): bool
    {
        $relativeTo = $dateTime ?? now();
        $votingPeriod = config('nom.accelerator.acceleratorProjectVotingPeriod');

        return $this->created_at->addSeconds($votingPeriod) >= $relativeTo;
    }

    public function getOpenForTimeAttribute(): string
    {
        $votingPeriod = config('nom.accelerator.acceleratorProjectVotingPeriod');

        return $this->created_at->addSeconds($votingPeriod)->diffForHumans(['parts' => 2], true);
    }

    public function getQuorumStatusAttribute(): string
    {
        if ($this->is_voting_open && ! $this->is_quorum_reached) {
            $votesText = Str::plural('vote', $this->total_more_votes_needed);

            return "{$this->total_more_votes_needed} {$votesText} needed in {$this->open_for_time}";
        }

        if (! $this->is_quorum_reached) {
            return 'Quorum not reached';
        }

        return 'Quorum reached';
    }

    public function getRawJsonAttribute(): ?AcceleratorProjectDTO
    {
        $cacheKey = $this->cacheKey('raw-json', 'updated_at');
        $data = Cache::get($cacheKey);

        try {
            $newData = app(ZenonSdk::class)->getProjectById($this->hash);
            Cache::put($cacheKey, $newData, now()->addDay());
            $data = $newData;
        } catch (Throwable $throwable) {
            // If API request fails, we do not need to do anything,
            // we will return previously cached data (retrieved at the start of the function).
        }

        return $data;
    }
}
