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
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
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
        'momentum_rewards',
        'delegate_rewards',
        'is_legacy',
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

    public function delegators(): BelongsToMany
    {
        return $this->belongsToMany(Account::class, 'nom_delegations')
            ->using(Delegation::class)
            ->withPivot('started_at', 'ended_at');
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

        return Number::abbreviate(app('znnToken')->getDisplayAmount($weight));
    }

    public function getDisplayQsrBurnAttribute(): string
    {
        return Number::abbreviate(app('qsrToken')->getDisplayAmount($this->qsr_burn));
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
}
