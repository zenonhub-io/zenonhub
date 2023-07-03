<?php

namespace App\Models\Nom;

use App;
use App\Models\Markable\Favorite;
use Cache;
use DigitalSloth\ZnnPhp\Utilities as ZnnUtilities;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Maize\Markable\Markable;
use Spatie\Sitemap\Contracts\Sitemapable;

class Account extends Model implements Sitemapable
{
    use HasFactory, Markable;

    public const ADDRESS_EMPTY = 'z1qqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqsggv2f';

    public const ADDRESS_PLASMA = 'z1qxemdeddedxplasmaxxxxxxxxxxxxxxxxsctrp';

    public const ADDRESS_PILLAR = 'z1qxemdeddedxpyllarxxxxxxxxxxxxxxxsy3fmg';

    public const ADDRESS_TOKEN = 'z1qxemdeddedxt0kenxxxxxxxxxxxxxxxxh9amk0';

    public const ADDRESS_SENTINEL = 'z1qxemdeddedxsentynelxxxxxxxxxxxxxwy0r2r';

    public const ADDRESS_SWAP = 'z1qxemdeddedxswapxxxxxxxxxxxxxxxxxxl4yww';

    public const ADDRESS_STAKE = 'z1qxemdeddedxstakexxxxxxxxxxxxxxxxjv8v62';

    public const ADDRESS_SPORK = 'z1qxemdeddedxsp0rkxxxxxxxxxxxxxxxx956u48';

    public const ADDRESS_ACCELERATOR = 'z1qxemdeddedxaccelerat0rxxxxxxxxxxp4tk22';

    public const ADDRESS_LIQUIDITY = 'z1qxemdeddedxlyquydytyxxxxxxxxxxxxflaaae';

    public const ADDRESS_BRIDGE = 'z1qxemdeddedxdrydgexxxxxxxxxxxxxxxmqgr0d';

    public const ADDRESS_HTLC = 'z1qxemdeddedxhtlcxxxxxxxxxxxxxxxxxygecvw';

    public const ADDRESS_PTLC = 'z1qxemdeddedxptlcxxxxxxxxxxxxxxxxx6lqady';

    public const ADDRESS_LIQUIDITY_PROGRAM_DISTRIBUTOR = 'z1qqw8f3qxx9zg92xgckqdpfws3dw07d26afsj74';

    public const EMBEDDED_CONTRACTS = [
        self::ADDRESS_PLASMA => 'Plasma contract',
        self::ADDRESS_PILLAR => 'Pillar contract',
        self::ADDRESS_TOKEN => 'Token contract',
        self::ADDRESS_SENTINEL => 'Sentinel contract',
        self::ADDRESS_SWAP => 'Swap contract',
        self::ADDRESS_STAKE => 'Stake contract',
        self::ADDRESS_SPORK => 'Spork contract',
        self::ADDRESS_ACCELERATOR => 'Accelerator contract',
        self::ADDRESS_LIQUIDITY => 'Liquidity contract',
        self::ADDRESS_BRIDGE => 'Bridge contract',
        self::ADDRESS_HTLC => 'HTLC contract',
        self::ADDRESS_PTLC => 'PTLC contract',
    ];

    protected static array $marks = [
        Favorite::class,
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'nom_accounts';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    public $fillable = [
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

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'first_active_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

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
        return $this->belongsTo(Chain::class, 'chain_id', 'id');
    }

    public function contract(): HasOne
    {
        return $this->hasOne(Contract::class, 'account_id', 'id')->latestOfMany();
    }

    public function delegations(): HasMany
    {
        return $this->hasMany(PillarDelegator::class, 'account_id', 'id');
    }

    public function fusions(): HasMany
    {
        return $this->hasMany(Fusion::class, 'from_account_id', 'id');
    }

    public function fuses(): HasMany
    {
        return $this->hasMany(Fusion::class, 'to_account_id', 'id');
    }

    public function plasma(): Builder
    {
        return Fusion::involvingAccount($this);
    }

    public function stakes(): HasMany
    {
        return $this->hasMany(Staker::class, 'account_id', 'id');
    }

    public function pillars(): HasMany
    {
        return $this->hasMany(Pillar::class, 'owner_id', 'id');
    }

    public function projects(): HasMany
    {
        return $this->hasMany(AcceleratorProject::class, 'owner_id', 'id');
    }

    public function sentinels(): HasMany
    {
        return $this->hasMany(Sentinel::class, 'owner_id', 'id');
    }

    public function tokens(): HasMany
    {
        return $this->hasMany(Token::class, 'owner_id', 'id');
    }

    public function sent_blocks(): HasMany
    {
        return $this->hasMany(AccountBlock::class, 'account_id', 'id');
    }

    public function received_blocks(): HasMany
    {
        return $this->hasMany(AccountBlock::class, 'to_account_id', 'id');
    }

    public function blocks(): Builder
    {
        return AccountBlock::involvingAccount($this);
    }

    public function latest_block(): HasOne
    {
        return $this->hasOne(AccountBlock::class, 'account_id', 'id')->latestOfMany();
    }

    public function first_block(): HasOne
    {
        return $this->hasOne(AccountBlock::class, 'account_id', 'id')->oldestOfMany();
    }

    public function rewards(): HasMany
    {
        return $this->hasMany(AccountReward::class, 'account_id', 'id');
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

    public function getDecodedPublicKeyAttribute()
    {
        if (! $this->public_key) {
            return '-';
        }
        $publicKey = base64_decode($this->public_key);

        return ZnnUtilities::toHex($publicKey);
    }

    public function getLiveBalancesAttribute()
    {
        return Cache::remember("address-{$this->id}-balances", 60, function () {
            $znn = App::make('zenon.api');
            $apiData = $znn->ledger->getAccountInfoByAddress($this->address);
            $balances = [];

            if ($apiData['status']) {
                foreach ($apiData['data']->balanceInfoMap as $token => $holdings) {
                    if ($holdings->balance > 0) {
                        if (! in_array($token, [Token::ZTS_ZNN, Token::ZTS_QSR])) {
                            $token = Token::whereZts($token)->first();
                            $balances[] = [
                                'name' => $token->name,
                                'token' => $token,
                                'balance' => $token->getDisplayAmount($holdings->balance),
                            ];
                        }
                    }
                }
            }

            return collect($balances);
        });
    }

    public function getDisplayZnnBalanceAttribute()
    {
        return znn_token()->getDisplayAmount($this->znn_balance);
    }

    public function getDisplayQsrBalanceAttribute()
    {
        return qsr_token()->getDisplayAmount($this->qsr_balance);
    }

    public function getDisplayUsdBalanceAttribute()
    {
        $znnBalance = float_number(znn_token()->getDisplayAmount($this->znn_balance));
        $qsrBalance = float_number(qsr_token()->getDisplayAmount($this->qsr_balance));

        $znnPrice = znn_price();
        $qsrPrice = qsr_price();

        $znnTotal = ($znnPrice * $znnBalance);
        $qsrTotal = ($qsrPrice * $qsrBalance);

        return number_format(($znnTotal + $qsrTotal), 2);
    }

    public function getDisplayZnnStakedAttribute()
    {
        return znn_token()->getDisplayAmount($this->znn_staked);
    }

    public function getDisplayQsrFusedAttribute()
    {
        return qsr_token()->getDisplayAmount($this->qsr_fused);
    }

    public function getDisplayTotalZnnBalanceAttribute()
    {
        return znn_token()->getDisplayAmount($this->total_znn_balance);
    }

    public function getDisplayTotalQsrBalanceAttribute()
    {
        return qsr_token()->getDisplayAmount($this->total_qsr_balance);
    }

    public function getDisplayTotalZnnRewardsAttribute()
    {
        return znn_token()->getDisplayAmount($this->total_znn_rewards);
    }

    public function getDisplayTotalQsrRewardsAttribute()
    {
        return qsr_token()->getDisplayAmount($this->total_qsr_rewards);
    }

    public function getActiveStakesAttribute()
    {
        return $this->stakes()->whereNull('ended_at')->get();
    }

    public function getActiveDelegationAttribute()
    {
        return $this->delegations()->whereNull('ended_at')->first();
    }

    public function getHasCustomLabelAttribute()
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

        return false;
    }

    public function getCustomLabelAttribute()
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
        $pillarCheck = $this->pillars()
            ->whereNull('revoked_at')
            ->first();

        return $pillarCheck->name ?? $this->address;
    }

    public function getShortAddressAttribute()
    {
        $start = mb_substr($this->address, 0, 6);
        $end = mb_substr($this->address, -6);

        return "{$start}....{$end}";
    }

    public function getActivePillarAttribute()
    {
        return $this->pillars()->whereNull('revoked_at')->latest()->first();
    }

    public function getPillarAttribute()
    {
        return $this->pillars()->latest()->first();
    }

    public function getActiveSentinelAttribute()
    {
        return $this->sentinels()->whereNull('revoked_at')->latest()->first();
    }

    public function getSentinelAttribute()
    {
        return $this->sentinels()->latest()->first();
    }

    public function getIsPillarWithdrawAddressAttribute()
    {
        $withdrawAddresses = Pillar::select('withdraw_id')->distinct()->pluck('withdraw_id');
        $pastWithdrawAddresses = PillarHistory::select('withdraw_id')->distinct()->pluck('withdraw_id');

        return $withdrawAddresses->merge($pastWithdrawAddresses)->unique()->contains($this->id);
    }

    public function getRawJsonAttribute()
    {
        return Cache::remember("account-{$this->id}-json", 10, function () {
            try {
                $znn = App::make('zenon.api');

                return $znn->ledger->getAccountInfoByAddress($this->address)['data'];
            } catch (\Exception $exception) {
                return null;
            }
        });
    }

    public function getFusedQsrAttribute()
    {
        return qsr_token()->getDisplayAmount($this->fuses->sum('amount'));
    }

    public function getPlasmaLevelAttribute()
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

    public function getIsStexTraderAttribute()
    {
        return $this->sent_blocks()
            ->whereHas('to_account', fn ($q) => $q->where('name', 'STEX Exchange'))
            ->count();
    }

    public function getIsFavouritedAttribute()
    {
        if ($user = auth()->user()) {
            return Favorite::findExisting($this, $user);
        }

        return false;
    }

    //
    // Methods

    public static function findByAddress(string $account): ?Account
    {
        return static::where('address', $account)->first();
    }

    public function tokenBalance($token, $decimals = null)
    {
        $holdings = $this->balances()
            ->where('token_id', $token->id)
            ->first();

        if ($holdings) {
            return $token->getDisplayAmount($holdings->pivot->balance, $decimals);
        }

        return 0;
    }

    public function tokenBalanceShare($token)
    {
        $holdings = $this->balances()
            ->where('token_id', $token->id)
            ->first();

        if ($holdings && $holdings->pivot->balance > 0) {
            $percentage = ($holdings->pivot->balance / $token->total_supply) * 100;

            return number_format($percentage, 2);
        }

        return 0;
    }

    public function displayZnnBalance($decimals = null)
    {
        return znn_token()->getDisplayAmount($this->znn_balance, $decimals);
    }

    public function displayQsrBalance($decimals = null)
    {
        return qsr_token()->getDisplayAmount($this->qsr_balance, $decimals);
    }

    public function displayZnnStaked($decimals = null)
    {
        return znn_token()->getDisplayAmount($this->znn_staked, $decimals);
    }

    public function displayQsrFused($decimals = null)
    {
        return qsr_token()->getDisplayAmount($this->qsr_fused, $decimals);
    }

    public function displayTotalZnnBalance($decimals = null)
    {
        return znn_token()->getDisplayAmount($this->total_znn_balance, $decimals);
    }

    public function displayTotalQsrBalance($decimals = null)
    {
        return qsr_token()->getDisplayAmount($this->total_qsr_balance, $decimals);
    }

    public function displayTotalZnnRewards($decimals = null)
    {
        return znn_token()->getDisplayAmount($this->total_znn_rewards, $decimals);
    }

    public function displayTotalQsrRewards($decimals = null)
    {
        return qsr_token()->getDisplayAmount($this->total_qsr_rewards, $decimals);
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
}
