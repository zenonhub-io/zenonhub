<?php

declare(strict_types=1);

namespace App\Models\Nom;

use App\DataTransferObjects\Nom\AccountDTO;
use App\Models\Favorite;
use App\Models\SocialProfile;
use App\Services\ZenonSdk\ZenonSdk;
use App\Traits\ModelCacheKeyTrait;
use Database\Factories\Nom\AccountFactory;
use DigitalSloth\ZnnPhp\Utilities as ZnnUtilities;
use Http;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Support\Facades\Cache;
use Maize\Markable\Markable;
use Spatie\Sitemap\Contracts\Sitemapable;
use Throwable;

class Account extends Model implements Sitemapable
{
    use HasFactory, Markable, ModelCacheKeyTrait;

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
    protected $table = 'nom_accounts';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'chain_id',
        'address',
        'public_key',
        'name',
        'znn_balance',
        'qsr_balance',
        'genesis_znn_balance',
        'genesis_qsr_balance',
        'is_embedded_contract',
        'first_active_at',
        'last_active_at',
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
            'znn_balance' => 'string',
            'qsr_balance' => 'string',
            'genesis_znn_balance' => 'string',
            'genesis_qsr_balance' => 'string',
            'znn_sent' => 'string',
            'znn_received' => 'string',
            'qsr_sent' => 'string',
            'qsr_received' => 'string',
            'znn_staked' => 'string',
            'qsr_fused' => 'string',
            'znn_rewards' => 'string',
            'qsr_rewards' => 'string',
            'first_active_at' => 'datetime',
            'last_active_at' => 'datetime',
        ];
    }

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory(): Factory
    {
        return AccountFactory::new();
    }

    //
    // Config

    public function toSitemapTag(): \Spatie\Sitemap\Tags\Url|string|array
    {
        return route('explorer.account', ['address' => $this->address]);
    }

    //
    // Relations

    public function chain(): BelongsTo
    {
        return $this->belongsTo(Chain::class);
    }

    public function contract(): HasOne
    {
        return $this->hasOne(Contract::class);
    }

    public function delegations(): BelongsToMany
    {
        return $this->belongsToMany(Pillar::class, 'nom_delegations')
            ->using(Delegation::class)
            ->withPivot('started_at', 'ended_at');
    }

    public function fusions(): HasMany
    {
        return $this->hasMany(Plasma::class, 'from_account_id');
    }

    public function plasma(): Builder
    {
        return Plasma::involvingAccount($this);
    }

    public function stakes(): HasMany
    {
        return $this->hasMany(Stake::class);
    }

    public function pillars(): HasMany
    {
        return $this->hasMany(Pillar::class, 'owner_id');
    }

    public function projects(): HasMany
    {
        return $this->hasMany(AcceleratorProject::class, 'owner_id');
    }

    public function sentinels(): HasMany
    {
        return $this->hasMany(Sentinel::class, 'owner_id');
    }

    public function tokens(): HasMany
    {
        return $this->hasMany(Token::class, 'owner_id');
    }

    public function sentBlocks(): HasMany
    {
        return $this->hasMany(AccountBlock::class, 'account_id');
    }

    public function receivedBlocks(): HasMany
    {
        return $this->hasMany(AccountBlock::class, 'to_account_id');
    }

    public function blocks(): Builder
    {
        return AccountBlock::involvingAccount($this);
    }

    public function latestBlock(): HasOne
    {
        return $this->hasOne(AccountBlock::class)->latestOfMany();
    }

    public function firstBlock(): HasOne
    {
        return $this->hasOne(AccountBlock::class)->oldestOfMany();
    }

    public function rewards(): HasMany
    {
        return $this->hasMany(AccountReward::class);
    }

    public function balances(): BelongsToMany
    {
        return $this->belongsToMany(
            Token::class,
            'nom_account_tokens',
            'account_id',
            'token_id'
        )->withPivot('balance', 'updated_at');
    }

    public function socialProfile(): MorphOne
    {
        return $this->morphOne(SocialProfile::class, 'profileable');
    }

    //
    // Scopes

    public function scopeWhereEmbedded($query)
    {
        $query->where('is_embedded_contract', '1');
    }

    public function scopeWhereNotEmbedded($query)
    {
        $query->where('is_embedded_contract', '0');
    }

    public function scopeTopByZnnBalance($query)
    {
        $query->orderBy('znn_balance', 'DESC');
    }

    public function scopeTopByQsrBalance($query)
    {
        $query->orderBy('qsr_balance', 'DESC');
    }

    public function scopeWhereListSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('address', $search);
        });
    }

    //
    // Attributes

    public function getDecodedPublicKeyAttribute(): string
    {
        if (! $this->public_key) {
            return '-';
        }
        $publicKey = base64_decode($this->public_key);

        return ZnnUtilities::toHex($publicKey);
    }

    public function getDisplayHeightAttribute(): string
    {
        return number_format($this->sent_blocks_count);
    }

    public function getDisplayZnnBalanceAttribute($decimals = null): string
    {
        return app('znnToken')->getFormattedAmount($this->znn_balance, $decimals);
    }

    public function getDisplayQsrBalanceAttribute($decimals = null): string
    {
        return app('qsrToken')->getFormattedAmount($this->qsr_balance, $decimals);
    }

    public function getDisplayUsdBalanceAttribute(): string
    {
        $znnBalance = app('znnToken')->getDisplayAmount($this->znn_balance);
        $qsrBalance = app('qsrToken')->getDisplayAmount($this->qsr_balance);

        $znnPrice = app('znnToken')->price;
        $qsrPrice = app('qsrToken')->price;

        $znnTotal = ($znnPrice * $znnBalance);
        $qsrTotal = ($qsrPrice * $qsrBalance);

        return number_format(($znnTotal + $qsrTotal), 2);
    }

    public function getDisplayZnnSentAttribute(): string
    {
        return app('znnToken')->getFormattedAmount($this->znn_sent);
    }

    public function getDisplayZnnReceivedAttribute(): string
    {
        return app('znnToken')->getFormattedAmount($this->znn_received);
    }

    public function getDisplayQsrSentAttribute(): string
    {
        return app('qsrToken')->getFormattedAmount($this->qsr_sent);
    }

    public function getDisplayQsrReceivedAttribute(): string
    {
        return app('qsrToken')->getFormattedAmount($this->qsr_received);
    }

    public function getDisplayZnnStakedAttribute(): string
    {
        return app('znnToken')->getFormattedAmount($this->znn_staked);
    }

    public function getDisplayQsrFusedAttribute(): string
    {
        return app('qsrToken')->getFormattedAmount($this->qsr_fused);
    }

    public function getDisplayZnnRewardsAttribute(): string
    {
        return app('znnToken')->getFormattedAmount($this->znn_rewards);
    }

    public function getDisplayQsrRewardsAttribute(): string
    {
        return app('qsrToken')->getFormattedAmount($this->qsr_rewards);
    }

    public function getPlasmaLevelAttribute(): string
    {
        $fusedQsr = app('qsrToken')->getDisplayAmount($this->qsr_fused);

        if ($fusedQsr > 0) {
            $fusedQsr = round($fusedQsr);

            if ($fusedQsr >= 120) {
                return 'High';
            }

            if ($fusedQsr >= 40) {
                return 'Medium';
            }

            return 'Low';
        }

        return 'None';
    }

    public function getDisplayDelegationPercentageShareAttribute(): string
    {
        $pillar = $this->delegations()
            ->wherePivotNull('ended_at')
            ->first();

        if (! $pillar) {
            return '-';
        }

        $weight = $pillar->weight;

        if ($pillar->revoked_at) {
            $weight = $pillar->activeDelegators->map(fn ($delegator) => $delegator->znn_balance)->sum();
        }

        if ($this->znn_balance && $weight) {
            $percentage = ($this->znn_balance / $weight) * 100;

            return number_format($percentage, 2);
        }

        return '0';
    }

    public function getActiveDelegationAttribute(): ?Model
    {
        return $this->delegations()->whereActive()->first();
    }

    public function getFundingBlockAttribute(): ?AccountBlock
    {
        $znnToken = app('znnToken');

        return $this->receivedBlocks()
            ->where('token_id', $znnToken->id)
            ->where('amount', '>', 0)
            ->oldest()
            ->first();
    }

    // TODO
    public function getIsPillarWithdrawAddressAttribute(): bool
    {
        $withdrawAddresses = Pillar::select('withdraw_account_id')->distinct()->pluck('withdraw_account_id');
        $pastWithdrawAddresses = PillarUpdateHistory::select('withdraw_account_id')->distinct()->pluck('withdraw_account_id');

        return $withdrawAddresses->merge($pastWithdrawAddresses)->unique()->contains($this->id);
    }

    // TODO
    public function getIsPillarProducerAddressAttribute(): bool
    {
        $producerAddresses = Pillar::select('producer_account_id')->distinct()->pluck('producer_account_id');
        $pastProducerAddresses = PillarUpdateHistory::select('producer_account_id')->distinct()->pluck('producer_account_id');

        return $producerAddresses->merge($pastProducerAddresses)->unique()->contains($this->id);
    }

    public function getHasCustomLabelAttribute(): bool
    {
        if ($this->name) {
            return true;
        }

        if ($user = auth()->user()) {

            // Check favorites
            $favorite = Favorite::findExisting($this, $user);
            if ($favorite) {
                return true;
            }

            // Check verified addresses
            $userAddress = $user->verifiedAccounts()
                ->where('address', $this->address)
                ->whereNotNull('nickname')
                ->first();

            if ($userAddress && $userAddress->pivot->nickname) {
                return true;
            }
        }

        $pillarCheck = $this->pillars()
            ->whereNull('revoked_at')
            ->first();

        if ($pillarCheck) {
            return true;
        }

        return false;
    }

    public function getCustomLabelAttribute(): string
    {
        if ($this->name) {
            return $this->name;
        }

        if ($user = auth()->user()) {

            //Check favorites
            $favorite = Favorite::findExisting($this, $user);
            if ($favorite) {
                return $favorite->label;
            }

            // Check verified addresses
            $userAddress = $user->verifiedAccounts()
                ->where('address', $this->address)
                ->whereNotNull('nickname')
                ->first();

            if ($userAddress && $userAddress->pivot->nickname) {
                return $userAddress->pivot->nickname;
            }
        }

        // If the address is a pillar return its name
        $pillar = $this->pillars()
            ->whereNull('revoked_at')
            ->first();

        return $pillar->name ?? $this->address;
    }

    public function getIsFavouriteAttribute(): bool
    {
        if ($user = auth()->user()) {
            return Favorite::has($this, $user);
        }

        return false;
    }

    public function getRawJsonAttribute(): ?AccountDTO
    {
        $cacheKey = $this->cacheKey('rawJson', 'last_active_at');
        $data = Cache::get($cacheKey);

        try {
            $newData = app(ZenonSdk::class)->getAccountInfoByAddress($this->address);
            Cache::forever($cacheKey, $newData);
            $data = $newData;
        } catch (Throwable $throwable) {
            // If API request fails, we do not need to do anything,
            // we will return previously cached data (retrieved at the start of the function).
        }

        return $data;
    }

    public function getIsFlaggedAttribute(): bool
    {
        $flaggedAccounts = array_keys(config('explorer.flagged_accounts'));

        return in_array($this->address, $flaggedAccounts);
    }

    public function getFlaggedDetailsAttribute(): string
    {
        return collect(config('explorer.flagged_accounts'))->where($this->account)->first();
    }

    public function getAvatarSvgAttribute()
    {
        $svg = Http::get('https://api.dicebear.com/9.x/identicon/svg', [
            'seed' => $this->address,
        ])->body();

        return $svg;
    }

    //
    // Methods

    public function tokenBalance($token, $decimals = null): string
    {
        $holdings = $this->balances()
            ->where('token_id', $token->id)
            ->first();

        if ($holdings) {
            return $token->getFormattedAmount($holdings->pivot->balance, $decimals);
        }

        return '0';
    }

    public function tokenBalanceShare($token): string
    {
        $holdings = $this->balances()
            ->where('token_id', $token->id)
            ->first();

        if ($holdings && $holdings->pivot->balance > 0) {
            $percentage = ($holdings->pivot->balance / $token->total_supply) * 100;

            return number_format($percentage, 2);
        }

        return '0';
    }
}
