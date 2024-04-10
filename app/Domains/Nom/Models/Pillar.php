<?php

declare(strict_types=1);

namespace App\Domains\Nom\Models;

use App\Models\Markable\Favorite;
use App\Services\ZenonSdk;
use App\Traits\FindByColumnTrait;
use App\Traits\ModelCacheKeyTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Number;
use Maize\Markable\Markable;
use Spatie\Sitemap\Contracts\Sitemapable;
use Throwable;

class Pillar extends Model implements Sitemapable
{
    use FindByColumnTrait, HasFactory, ModelCacheKeyTrait;
    //use ModelCacheKeyTrait, FindByColumnTrait, HasFactory, Markable;

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
    protected $table = 'nom_pillars';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'chain_id',
        'owner_id',
        'producer_account_id',
        'withdraw_account_id',
        'name',
        'slug',
        'qsr_burn',
        'weight',
        'produced_momentums',
        'expected_momentums',
        'missed_momentums',
        'momentum_rewards',
        'delegate_rewards',
        'az_engagement',
        'az_avg_vote_time',
        'avg_momentums_produced',
        'total_momentums_produced',
        'is_legacy',
        'revoked_at',
        'created_at',
        'updated_at',
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
            'revoked_at' => 'datetime',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    //
    // config

    public function toSitemapTag(): \Spatie\Sitemap\Tags\Url|string|array
    {
        return route('pillars.detail', ['slug' => $this->slug]);
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

    public function producerAccount(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    public function withdrawAccount(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    public function orchestrator(): HasOne
    {
        return $this->hasOne(Orchestrator::class);
    }

    public function history(): HasMany
    {
        return $this->hasMany(PillarHistory::class);
    }

    public function delegators(): HasMany
    {
        return $this->hasMany(PillarDelegator::class);
    }

    public function azVotes(): HasMany
    {
        return $this->hasMany(AcceleratorVote::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(PillarMessage::class);
    }

    //
    // Scopes

    public function scopeIsActive($query)
    {
        return $query->whereNull('revoked_at');
    }

    public function scopeIsProducing($query)
    {
        return $query->where('missed_momentums', '<=', config('zenon.pillar_missed_momentum_limit'))
            ->whereNull('revoked_at');
    }

    public function scopeIsNotProducing($query)
    {
        return $query->where('missed_momentums', '>', config('zenon.pillar_missed_momentum_limit'));
    }

    public function scopeIsRevoked($query)
    {
        return $query->whereNotNull('revoked_at');
    }

    public function scopeIsTop30($query)
    {
        return $query->orderBy('weight', 'desc')
            ->limit(30);
    }

    public function scopeIsNotTop30($query)
    {
        $top30 = self::isActive()->isTop30()->pluck('id');

        return $query->whereNotIn('id', $top30);
    }

    public function scopeWhereListSearch($query, $search)
    {
        return $query->where('name', 'LIKE', "%{$search}%");
    }

    //
    // Attributes

    public function getDisplayWeightAttribute(): string
    {
        $weight = $this->weight;

        if ($this->revoked_at) {
            $weight = $this->active_delegators->map(fn ($delegator) => $delegator->account->znn_balance)->sum();
        }

        return Number::abbreviate(znn_token()->getDisplayAmount($weight));
    }

    public function getDisplayQsrBurnAttribute(): string
    {
        return Number::abbreviate(qsr_token()->getDisplayAmount($this->qsr_burn));
    }

    public function getRankAttribute(): string
    {
        return Cache::remember("{$this->cacheKey()}|pillar-rank", 60 * 10, function () {
            if ($this->revoked_at || ! $this->weight) {
                return '-';
            }

            $pillars = self::whereNull('revoked_at')->orderBy('weight', 'desc')->get();
            $data = $pillars->where('id', $this->id);

            return $data->keys()->first() + 1;
        });
    }

    public function getProducedMomentumsPercentageAttribute(): float
    {
        if ($this->expected_momentums) {
            $percentage = ($this->produced_momentums * 100) / $this->expected_momentums;

            return round($percentage, 1);
        }

        return 0;
    }

    public function getActiveDelegatorsAttribute(): ?Collection
    {
        return $this->delegators()
            ->isActive()
            ->withBalance()
            ->get();
    }

    public function getActiveDelegatorsCountAttribute(): int
    {
        return $this->delegators()
            ->isActive()
            ->withBalance()
            ->count();
    }

    public function getPreviousHistoryAttribute(): ?Model
    {
        return $this->history()
            ->orderByDesc('updated_at')
            ->offset(1)
            ->limit(1)
            ->first();
    }

    public function getDidRewardsChangeAttribute(): bool
    {
        return ! $this->previous_history ||
            (
                $this->previous_history->momentum_rewards !== $this->momentum_rewards ||
                $this->previous_history->delegate_rewards !== $this->delegate_rewards
            );
    }

    public function getRawJsonAttribute(): array
    {
        $updateCache = true;
        $cacheKey = "nom.pillar.rawJson.{$this->id}";

        try {
            $znn = App::make(ZenonSdk::class);
            $data = $znn->pillar->getByOwner($this->owner->address)['data'][0];
        } catch (Throwable $throwable) {
            $updateCache = false;
            $data = Cache::get($cacheKey);
        }

        if ($updateCache) {
            Cache::forever($cacheKey, $data);
        }

        return $data;
    }

    public function getIsProducingAttribute(): bool
    {
        return is_null($this->revoked_at) && $this->missed_momentums <= config('zenon.pillar_missed_momentum_limit');
    }

    public function getAzStatusIndicatorAttribute(): string
    {
        if ($this->az_engagement < 1) {
            return 'danger';
        }

        if ($this->az_engagement < 75) {
            return 'warning';
        }

        return 'success';
    }

    public function getDisplayAzAvgVoteTimeAttribute(): string
    {
        if ($this->az_avg_vote_time) {
            return now()->subSeconds($this->az_avg_vote_time)->diffForHumans(['parts' => 2], true);
        }

        return '-';
    }

    public function getIsFavouritedAttribute(): bool
    {
        if ($user = auth()->user()) {
            return Favorite::findExisting($this, $user);
        }

        return false;
    }

    public function getDisplayRevocableInAttribute(): string
    {
        if (! $this->raw_json) {
            return '-';
        }

        if ($this->raw_json?->revokeCooldown > 0) {
            return now()->addSeconds($this->raw_json->revokeCooldown)->diffForHumans(['parts' => 2], true);
        }

        return 'Now';
    }

    //
    // Methods

    public function updateAzEngagementScores()
    {
        $totalProjects = AcceleratorProject::where('created_at', '>=', $this->created_at)->count();
        $totalPhases = AcceleratorPhase::where('created_at', '>=', $this->created_at)->count();
        $totalVotableItems = ($totalProjects + $totalPhases);

        $this->az_engagement = 0;

        if ($totalVotableItems > 0) {

            // Make sure the vote item was created after the pillar
            // Projects/phases might be open after a pillar spawned, dont include these
            $votes = $this->azVotes()->get();
            $totalVotes = $votes->map(function ($vote) {
                if ($vote->votable->created_at >= $this->created_at) {
                    return 1;
                }

                return 0;
            })->sum();

            // If a pillar has more votes than projects ensure the pillar doenst get over 100% engagement
            if ($totalVotes > $totalVotableItems) {
                $totalVotes = $totalVotableItems;
            }

            $percentage = ($totalVotes * 100) / $totalVotableItems;
            $this->az_engagement = round($percentage, 1);
        }

        $averageVoteTime = $this->azVotes->map(fn ($vote) => $vote->created_at->timestamp - $vote->votable->created_at->timestamp)->average();

        $this->az_avg_vote_time = $averageVoteTime;
        $this->save();
    }
}
