<?php

declare(strict_types=1);

namespace App\Domains\Nom\Models;

use App\Domains\Nom\Enums\NetworkTokensEnum;
use App\Models\Markable\Favorite;
use App\Services\ZenonSdk;
use App\Traits\FindByColumnTrait;
use App\Traits\ModelCacheKeyTrait;
use DigitalSloth\ZnnPhp\Utilities as ZnnUtilities;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use Maize\Markable\Markable;
use Spatie\Sitemap\Contracts\Sitemapable;
use Throwable;

class Account extends Model implements Sitemapable
{
    //use FindByColumnTrait, HasFactory, Markable;
    use FindByColumnTrait, HasFactory, ModelCacheKeyTrait;

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
        'total_znn_rewards',
        'total_qsr_rewards',
        'is_embedded_contract',
        'first_active_at',
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
            'first_active_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    //
    // Static methods

    public static function getAllPillarWithdrawAddresses()
    {
        $withdrawAddresses = Pillar::select('withdraw_id')->distinct()->pluck('withdraw_id');
        $pastWithdrawAddresses = PillarHistory::select('withdraw_id')->distinct()->pluck('withdraw_id');

        return $withdrawAddresses->merge($pastWithdrawAddresses)->unique();
    }

    public static function getAllPillarProducerAddresses()
    {
        $producerAddresses = Pillar::select('producer_id')->distinct()->pluck('producer_id');
        $pastProducerAddresses = PillarHistory::select('producer_id')->distinct()->pluck('producer_id');

        return $producerAddresses->merge($pastProducerAddresses)->unique();
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

    public function delegations(): HasMany
    {
        return $this->hasMany(PillarDelegator::class);
    }

    public function fusions(): HasMany
    {
        return $this->hasMany(Plasma::class, 'from_account_id');
    }

    public function fuses(): HasMany
    {
        return $this->hasMany(Plasma::class, 'to_account_id', 'id');
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
        return $this->hasOne(AccountBlock::class, 'account_id')->latestOfMany();
    }

    public function firstBlock(): HasOne
    {
        return $this->hasOne(AccountBlock::class, 'account_id')->oldestOfMany();
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

    //
    // Scopes

    public function scopeIsEmbedded($query)
    {
        $query->where('is_embedded_contract', '1');
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

    public function getLiveBalancesAttribute(): Collection
    {
        return Cache::rememberForever("{$this->cacheKey()}|getLiveBalancesAttribute", function () {
            $znn = App::make(ZenonSdk::class);
            $apiData = $znn->ledger->getAccountInfoByAddress($this->address);
            $balances = [];

            if ($apiData['status']) {
                foreach ($apiData['data']->balanceInfoMap as $token => $holdings) {
                    if (! $holdings->balance) {
                        continue;
                    }

                    if (in_array($token, [NetworkTokensEnum::ZNN->value, NetworkTokensEnum::QSR->value])) {
                        continue;
                    }

                    $token = Token::whereZts($token)->first();
                    $balances[] = [
                        'name' => $token->name,
                        'token' => $token,
                        'balance' => $token->getDisplayAmount($holdings->balance),
                    ];
                }
            }

            return collect($balances);
        });
    }

    public function getDisplayZnnBalanceAttribute($decimals = null): string
    {
        return znn_token()->getFormattedAmount($this->znn_balance, $decimals);
    }

    public function getDisplayQsrBalanceAttribute($decimals = null): string
    {
        return qsr_token()->getFormattedAmount($this->qsr_balance, $decimals);
    }

    public function getDisplayUsdBalanceAttribute(): string
    {
        $znnBalance = znn_token()->getDisplayAmount($this->znn_balance);
        $qsrBalance = qsr_token()->getDisplayAmount($this->qsr_balance);

        $znnPrice = znn_price();
        $qsrPrice = qsr_price();

        $znnTotal = ($znnPrice * $znnBalance);
        $qsrTotal = ($qsrPrice * $qsrBalance);

        return number_format(($znnTotal + $qsrTotal), 2);
    }

    public function getDisplayZnnStakedAttribute($decimals = null): string
    {
        return znn_token()->getFormattedAmount($this->znn_staked, $decimals);
    }

    public function getDisplayQsrFusedAttribute($decimals = null): string
    {
        return qsr_token()->getFormattedAmount($this->qsr_fused, $decimals);
    }

    public function getDisplayTotalZnnBalanceAttribute($decimals = null): string
    {
        return znn_token()->getFormattedAmount($this->total_znn_balance, $decimals);
    }

    public function getDisplayTotalQsrBalanceAttribute($decimals = null): string
    {
        return qsr_token()->getFormattedAmount($this->total_qsr_balance, $decimals);
    }

    public function getDisplayTotalZnnRewardsAttribute($decimals = null): string
    {
        return znn_token()->getFormattedAmount($this->total_znn_rewards, $decimals);
    }

    public function getDisplayTotalQsrRewardsAttribute($decimals = null): string
    {
        return qsr_token()->getFormattedAmount($this->total_qsr_rewards, $decimals);
    }

    public function getFusedQsrAttribute(): string
    {
        return qsr_token()->getFormattedAmount($this->fuses->sum('amount'));
    }

    public function getPlasmaLevelAttribute(): string
    {
        $fusedQsr = float_number($this->fused_qsr);

        if ($fusedQsr >= 120) {
            return 'High';
        }

        if ($fusedQsr >= 40) {
            return 'Medium';
        }

        return 'Low';
    }

    public function getActiveStakesAttribute(): ?Model
    {
        return $this->stakes()->isActive()->get();
    }

    public function getActiveDelegationAttribute(): ?Model
    {
        return $this->delegations()->isActive()->first();
    }

    public function getActivePillarAttribute(): ?Model
    {
        return $this->pillars()->isActive()->latest()->first();
    }

    public function getPillarAttribute(): ?Model
    {
        return $this->pillars()->latest()->first();
    }

    public function getActiveSentinelAttribute(): ?Model
    {
        return $this->sentinels()->isActive()->latest()->first();
    }

    public function getSentinelAttribute(): ?Model
    {
        return $this->sentinels()->latest()->first();
    }

    public function getIsPillarWithdrawAddressAttribute(): bool
    {
        $withdrawAddresses = Pillar::select('withdraw_id')->distinct()->pluck('withdraw_id');
        $pastWithdrawAddresses = PillarHistory::select('withdraw_id')->distinct()->pluck('withdraw_id');

        return $withdrawAddresses->merge($pastWithdrawAddresses)->unique()->contains($this->id);
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
            $userAddress = $user->nom_accounts()
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

        //        $pillarProducer = Pillar::where('producer_id', $this->id)
        //            ->whereNull('revoked_at')
        //            ->first();
        //
        //        if ($pillarProducer) {
        //            return true;
        //        }

        return false;
    }

    public function getCustomLabelAttribute(): string
    {
        if ($this->name) {
            return $this->name;
        }

        if ($user = auth()->user()) {

            // Check favorites
            $favorite = Favorite::findExisting($this, $user);
            if ($favorite) {
                return $favorite->label;
            }

            // Check verified addresses
            $userAddress = $user->nom_accounts()
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

    public function getShortAddressAttribute(): string
    {
        $start = mb_substr($this->address, 0, 6);
        $end = mb_substr($this->address, -6);

        return "{$start}....{$end}";
    }

    public function getRawJsonAttribute(): array
    {
        $updateCache = true;
        $cacheKey = "nom.account.rawJson.{$this->id}";

        try {
            $znn = App::make(ZenonSdk::class);
            $data = $znn->ledger->getAccountInfoByAddress($this->address)['data'];
        } catch (Throwable $throwable) {
            $updateCache = false;
            $data = Cache::get($cacheKey);
        }

        if ($updateCache) {
            Cache::forever($cacheKey, $data);
        }

        return $data;
    }

    public function getIsStexTraderAttribute(): bool
    {
        return $this->sentBlocks()
            ->whereRelation('toAccount', 'name', 'STEX Exchange')
            ->count() > 0;
    }

    public function getIsFavouritedAttribute(): bool
    {
        if ($user = auth()->user()) {
            return Favorite::findExisting($this, $user);
        }

        return false;
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
