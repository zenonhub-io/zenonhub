<?php

declare(strict_types=1);

namespace App\Domains\Nom\Models;

use App\Domains\Nom\Enums\EmbeddedContractsEnum;
use App\Domains\Nom\Enums\NetworkTokensEnum;
use App\Domains\Nom\Services\ZenonSdk;
use App\Models\Markable\Favorite;
use Brick\Math\BigDecimal;
use Brick\Math\RoundingMode;
use Database\Factories\Domains\Nom\TokenFactory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Cache;
use Maize\Markable\Markable;
use Spatie\Sitemap\Contracts\Sitemapable;
use Throwable;

class Token extends Model implements Sitemapable
{
    //use Markable;
    use HasFactory;

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
    protected $table = 'nom_tokens';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'chain_id',
        'owner_id',
        'name',
        'symbol',
        'domain',
        'token_standard',
        'total_supply',
        'max_supply',
        'decimals',
        'is_burnable',
        'is_mintable',
        'is_utility',
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
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory(): Factory
    {
        return TokenFactory::new();
    }

    //
    // Methods

    public static function findByZtsWithHolders(string $zts): ?Token
    {
        return static::withCount(['holders' => function ($q) {
            $q->where('balance', '>', '0');
        }])->where('token_standard', $zts)->first();
    }

    //
    // config

    public function toSitemapTag(): \Spatie\Sitemap\Tags\Url|string|array
    {
        return route('explorer.token', ['zts' => $this->token_standard]);
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

    public function holders(): BelongsToMany
    {
        return $this->belongsToMany(
            Account::class,
            'nom_account_tokens',
            'token_id',
            'account_id'
        )->withPivot('balance', 'updated_at');
    }

    public function prices(): BelongsToMany
    {
        return $this->belongsToMany(
            Currency::class,
            'nom_token_prices',
            'token_id',
            'currency_id'
        )->withPivot('value', 'timestamp');
    }

    public function mints(): HasMany
    {
        return $this->hasMany(TokenMint::class);
    }

    public function burns(): HasMany
    {
        return $this->hasMany(TokenBurn::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(AccountBlock::class);
    }

    public function bridgeNetworkTokens(): HasMany
    {
        return $this->hasMany(BridgeNetworkToken::class);
    }

    //
    // Scopes

    public function scopeWhereListSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('name', $search)
                ->orWhere('symbol', $search)
                ->orWhere('token_standard', $search);
        });
    }

    public function scopeWhereNetwork($query)
    {
        return $query->whereRelation('owner', 'is_embedded_contract', '1');
    }

    //
    // Attributes

    public function getShortTokenStandardAttribute(): string
    {
        return short_hash($this->token_standard, 5);
    }

    public function getTotalMintedAttribute(): float
    {
        return $this->mints()->sum('amount');
    }

    public function getDisplayTotalMintedAttribute(): string
    {
        $minted = $this->mints()->sum('amount');

        return $this->getFormattedAmount($minted);
    }

    public function getTotalBurnedAttribute(): float
    {
        return $this->burns()->sum('amount');
    }

    public function getDisplayTotalBurnedAttribute(): string
    {
        $burned = $this->burns()->sum('amount');

        return $this->getFormattedAmount($burned);
    }

    public function getTotalLockedAttribute(): float
    {
        $totalLocked = 0;

        if ($this->token_standard === NetworkTokensEnum::ZNN->value) {
            $pillarLockup = Account::firstWhere('address', EmbeddedContractsEnum::PILLAR->value)->znn_balance;
            $sentinelLockup = Account::firstWhere('address', EmbeddedContractsEnum::SENTINEL->value)->znn_balance;
            $stakingLockup = Account::firstWhere('address', EmbeddedContractsEnum::STAKE->value)->znn_balance;
            $totalLocked = ($pillarLockup + $sentinelLockup + $stakingLockup);
        }

        if ($this->token_standard === NetworkTokensEnum::QSR->value) {
            $sentinelLockup = Account::firstWhere('address', EmbeddedContractsEnum::SENTINEL->value)->qsr_balance;
            $plasmaLockup = Account::firstWhere('address', EmbeddedContractsEnum::PLASMA->value)->qsr_balance;
            $totalLocked = ($sentinelLockup + $plasmaLockup);
        }

        if ($this->token_standard === NetworkTokensEnum::LP_ZNN_ETH->value) {
            $liquidityAccount = Account::firstWhere('address', EmbeddedContractsEnum::LIQUIDITY->value);
            $totalLocked = $liquidityAccount->balances()
                ->where('token_id', $this->id)
                ->first()?->pivot->balance;
        }

        return $totalLocked;
    }

    public function getDisplayTotalLockedAttribute(): string
    {
        return $this->getFormattedAmount($this->total_locked);
    }

    public function getHasLockedTokensAttribute(): bool
    {
        return in_array($this->token_standard, [
            NetworkTokensEnum::ZNN->value,
            NetworkTokensEnum::QSR->value,
            NetworkTokensEnum::LP_ZNN_ETH->value,
        ]);
    }

    public function getHasCustomLabelAttribute(): bool
    {
        return ($user = auth()->user()) && Favorite::findExisting($this, $user);
    }

    public function getCustomLabelAttribute(): string
    {
        if ($user = auth()->user()) {
            $favorite = Favorite::findExisting($this, $user);
            if ($favorite) {
                return $favorite->label;
            }
        }

        return $this->name;
    }

    public function getIsFavouritedAttribute(): bool
    {
        if ($user = auth()->user()) {
            return Favorite::findExisting($this, $user);
        }

        return false;
    }

    public function getUsdPriceAttribute(): float
    {
        if ($this->token_standard === NetworkTokensEnum::ZNN->value) {
            return znn_price();
        }

        if ($this->token_standard === NetworkTokensEnum::QSR->value) {
            return qsr_price();
        }

        return 0;
    }

    public function getRawJsonAttribute(): array
    {
        $cacheKey = $this->cacheKey('rawJson');
        $data = Cache::get($cacheKey);

        try {
            $newData = app(ZenonSdk::class)->getByZts($this->token_standard);
            Cache::forever($cacheKey, $newData);
            $data = $newData;
        } catch (Throwable $throwable) {
            // If API request fails, we do not need to do anything,
            // we will return previously cached data (retrieved at the start of the function).
        }

        return $data;
    }

    public function getDisplayAmount(mixed $amount): int|float
    {
        if (is_null($amount)) {
            return 0;
        }

        $number = BigDecimal::of(10)->power($this->decimals);
        $bigDecimal = BigDecimal::of($amount)->dividedBy($number, $this->decimals);
        $number = $bigDecimal->toScale($this->decimals, RoundingMode::DOWN);

        if ($this->decimals === 0 || $bigDecimal->getScale() === 0) {
            return $bigDecimal->toBigInteger()->toInt();
        }

        return $number->toFloat();
    }

    public function getFormattedAmount($amount, $numDecimals = null, ?string $decimalsSeparator = '.', ?string $thousandsSeparator = ','): string
    {
        if (is_null($amount)) {
            return '-';
        }

        $number = $this->getDisplayAmount($amount);
        $outputDecimals = (! is_null($numDecimals) ? $numDecimals : $this->decimals);
        $numberFormatted = number_format($number, $outputDecimals, $decimalsSeparator, $thousandsSeparator);

        return rtrim(rtrim($numberFormatted, '0'), '.');
    }

    public function getDisplayUsdAmount($amount): float
    {
        return $this->getDisplayAmount($amount) * $this->usd_price;
    }
}
