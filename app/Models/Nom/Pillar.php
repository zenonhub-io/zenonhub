<?php

declare(strict_types=1);

namespace App\Models\Nom;

use App\DataTransferObjects\Nom\PillarDTO;
use App\Models\Markable\Favorite;
use App\Models\SocialProfile;
use App\Services\ZenonSdk;
use App\Traits\ModelCacheKeyTrait;
use Database\Factories\Nom\PillarFactory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Number;
use Maize\Markable\Markable;
use Spatie\Sitemap\Contracts\Sitemapable;
use Throwable;

class Pillar extends Model implements Sitemapable
{
    use HasFactory, ModelCacheKeyTrait;
    //use Markable;

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
        'rank',
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
            'qsr_burn' => 'string',
            'is_legacy' => 'boolean',
            'revoked_at' => 'datetime',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory(): Factory
    {
        return PillarFactory::new();
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

    public function momentums(): HasMany
    {
        return $this->hasMany(Momentum::class, 'producer_pillar_id');
    }

    public function updateHistory(): HasMany
    {
        return $this->hasMany(PillarUpdateHistory::class);
    }

    public function statHistory(): HasMany
    {
        return $this->hasMany(PillarStatHistory::class);
    }

    public function delegators(): BelongsToMany
    {
        return $this->belongsToMany(Account::class, 'nom_delegations')
            ->using(Delegation::class)
            ->withPivot('started_at', 'ended_at');
    }

    public function activeDelegators(): BelongsToMany
    {
        return $this->belongsToMany(Account::class, 'nom_delegations')
            ->using(Delegation::class)
            ->withPivot('started_at', 'ended_at')
            ->wherePivotNull('ended_at')
            ->where('znn_balance', '>', '0');
    }

    public function votes(): HasMany
    {
        return $this->hasMany(Vote::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(PillarMessage::class);
    }

    public function socialProfile(): MorphOne
    {
        return $this->morphOne(SocialProfile::class, 'profileable');
    }

    //
    // Scopes

    public function scopeWhereActive($query)
    {
        return $query->whereNull('revoked_at');
    }

    public function scopeWhereProducing($query)
    {
        return $query->where('missed_momentums', '<=', config('zenon-hub.pillar_missed_momentum_limit'))
            ->whereNull('revoked_at');
    }

    public function scopeWhereNotProducing($query)
    {
        return $query->where('missed_momentums', '>', config('zenon-hub.pillar_missed_momentum_limit'));
    }

    public function scopeWhereRevoked($query)
    {
        return $query->whereNotNull('revoked_at');
    }

    public function scopeWhereTop30($query)
    {
        return $query->orderBy('weight', 'desc')
            ->limit(30);
    }

    public function scopeWhereNotTop30($query)
    {
        $top30 = self::whereActive()->whereTop30()->pluck('id');

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

        if ($weight < 1 * NOM_DECIMALS) {
            return (string) $weight;
        }

        return Number::abbreviate(app('znnToken')->getDisplayAmount($weight), 1);
    }

    public function getDisplayQsrBurnAttribute(): string
    {
        return Number::abbreviate(app('qsrToken')->getDisplayAmount($this->qsr_burn));
    }

    public function getDisplayRankAttribute(): string
    {
        if ($this->revoked_at || ! $this->weight) {
            return '-';
        }

        return (string) ($this->rank + 1);
    }

    public function getProducedMomentumsPercentageAttribute(): float
    {
        if ($this->expected_momentums) {
            $percentage = ($this->produced_momentums * 100) / $this->expected_momentums;

            return round($percentage, 1);
        }

        return 0;
    }

    public function getPreviousHistoryAttribute(): ?Model
    {
        return $this->updateHistory()
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

    public function getRawJsonAttribute(): PillarDTO
    {
        $cacheKey = $this->cacheKey('rawJson');
        $data = Cache::get($cacheKey);

        try {
            $newData = app(ZenonSdk::class)
                ->getPillarByOwner($this->owner->address);
            Cache::forever($cacheKey, $newData);
            $data = $newData;
        } catch (Throwable $throwable) {
            // If API request fails, we do not need to do anything,
            // we will return previously cached data (retrieved at the start of the function).
        }

        return $data;
    }

    public function getIsProducingAttribute(): bool
    {
        return is_null($this->revoked_at) && $this->missed_momentums <= config('zenon.pillar_missed_momentum_limit');
    }

    public function getStatusColourAttribute(): string
    {
        if ($this->revoked_at) {
            return 'danger';
        }

        if ($this->is_producing) {
            return 'success';
        }

        return 'warning';
    }

    public function getStatusTextAttribute(): string
    {
        if ($this->revoked_at) {
            return __('Revoked');
        }

        if ($this->is_producing) {
            return __('Active');
        }

        return __('Not producing momentums');
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

    public function getIsRevokableAttribute(?Carbon $dateTime): bool
    {
        $lockTimeWindow = config('nom.pillar.epochLockTime');
        $revokeTimeWindow = config('nom.pillar.epochRevokeTime');
        $relativeTo = $dateTime ?? now();
        $epochTime = ($relativeTo->timestamp - $this->created_at->timestamp) % ($lockTimeWindow + $revokeTimeWindow);

        return $epochTime >= $lockTimeWindow;
    }

    public function getTimeUntilRevokableAttribute(?Carbon $dateTime): string
    {
        if ($this->getIsRevokableAttribute($dateTime)) {
            return 'Now';
        }

        $lockTimeWindow = config('nom.pillar.epochLockTime');
        $revokeTimeWindow = config('nom.pillar.epochRevokeTime');
        $relativeTo = $dateTime ?? now();
        $epochTime = ($relativeTo->timestamp - $this->created_at->timestamp) % ($lockTimeWindow + $revokeTimeWindow);
        $revokeCooldown = $lockTimeWindow - $epochTime;

        return Carbon::parse($relativeTo)->addSeconds($revokeCooldown)->diffForHumans(['parts' => 2], true);
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
