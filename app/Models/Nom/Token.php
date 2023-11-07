<?php

namespace App\Models\Nom;

use App\Models\Markable\Favorite;
use App\Services\ZenonSdk;
use Brick\Math\BigDecimal;
use Brick\Math\RoundingMode;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\App;
use Maize\Markable\Markable;
use Spatie\Sitemap\Contracts\Sitemapable;

class Token extends Model implements Sitemapable
{
    use HasFactory, Markable;

    public const ZTS_EMPTY = 'zts1qqqqqqqqqqqqqqqqtq587y';

    public const ZTS_ZNN = 'zts1znnxxxxxxxxxxxxx9z4ulx';

    public const ZTS_QSR = 'zts1qsrxxxxxxxxxxxxxmrhjll';

    public const ZTS_LP_ETH = 'zts17d6yr02kh0r9qr566p7tg6';

    protected static array $marks = [
        Favorite::class,
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'nom_tokens';

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

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

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
        return $this->belongsTo(Chain::class, 'chain_id', 'id');
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'owner_id', 'id');
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

    public function mints(): HasMany
    {
        return $this->hasMany(TokenMint::class, 'token_id', 'id');
    }

    public function burns(): HasMany
    {
        return $this->hasMany(TokenBurn::class, 'token_id', 'id');
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(AccountBlock::class, 'token_id', 'id');
    }

    //
    // Scopes

    public function scopeWhereZts($query, $zts)
    {
        return $query->where('token_standard', $zts);
    }

    public function scopeWhereListSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('name', $search)
                ->orWhere('symbol', $search)
                ->orWhere('token_standard', $search);
        });
    }

    //
    // Attributes

    public function getShortTokenStandardAttribute()
    {
        return short_hash($this->token_standard, 5);
    }

    public function getTotalMintedAttribute()
    {
        return $this->mints()->sum('amount');
    }

    public function getDisplayTotalMintedAttribute()
    {
        return $this->getDisplayAmount($this->mints()->sum('amount'));
    }

    public function getTotalBurnedAttribute()
    {
        return $this->burns()->sum('amount');
    }

    public function getDisplayTotalBurnedAttribute()
    {
        return $this->getDisplayAmount($this->burns()->sum('amount'));
    }

    public function getTotalLockedAttribute()
    {
        $totalLocked = 0;

        if ($this->token_standard === self::ZTS_ZNN) {
            $pillarLockup = Account::findByAddress(Account::ADDRESS_PILLAR)->znn_balance;
            $sentinelLockup = Account::findByAddress(Account::ADDRESS_SENTINEL)->znn_balance;
            $stakingLockup = Account::findByAddress(Account::ADDRESS_STAKE)->znn_balance;
            $totalLocked = ($pillarLockup + $sentinelLockup + $stakingLockup);
        }

        if ($this->token_standard === self::ZTS_QSR) {
            $sentinelLockup = Account::findByAddress(Account::ADDRESS_SENTINEL)->qsr_balance;
            $plasmaLockup = Account::findByAddress(Account::ADDRESS_PLASMA)->qsr_balance;
            $totalLocked = ($sentinelLockup + $plasmaLockup);
        }

        if ($this->token_standard === self::ZTS_LP_ETH) {
            $liquidityAccount = Account::findByAddress(Account::ADDRESS_LIQUIDITY);
            $totalLocked = $liquidityAccount->balances()
                ->where('token_id', $this->id)
                ->first()?->pivot->balance;
        }

        return $totalLocked;
    }

    public function getDisplayTotalLockedAttribute()
    {
        return $this->getDisplayAmount($this->total_locked);
    }

    public function getHasLockedTokensAttribute()
    {
        return in_array($this->token_standard, [self::ZTS_ZNN, self::ZTS_QSR, self::ZTS_LP_ETH]);
    }

    public function getHasCustomLabelAttribute()
    {
        if ($user = auth()->user()) {
            $favorite = Favorite::findExisting($this, $user);
            if ($favorite) {
                return true;
            }
        }

        return false;
    }

    public function getCustomLabelAttribute()
    {
        if ($user = auth()->user()) {
            $favorite = Favorite::findExisting($this, $user);
            if ($favorite) {
                return $favorite->label;
            }
        }

        return $this->name;
    }

    public function getIsFavouritedAttribute()
    {
        if ($user = auth()->user()) {
            return Favorite::findExisting($this, $user);
        }

        return false;
    }

    public function getUsdPriceAttribute()
    {
        if ($this->token_standard === self::ZTS_ZNN) {
            return znn_price();
        }

        if ($this->token_standard === self::ZTS_QSR) {
            return qsr_price();
        }

        return 0;
    }

    //
    // Methods

    public static function findByZts(string $zts): ?Token
    {
        return static::where('token_standard', $zts)->first();
    }

    public static function findByZtsWithHolders(string $zts): ?Token
    {
        return static::withCount(['holders' => function ($q) {
            $q->where('balance', '>', '0');
        }])->where('token_standard', $zts)->first();
    }

    public function getDisplayAmount($amount, $numDecimals = null, ?string $decimalsSeparator = '.', ?string $thousandsSeparator = ',')
    {
        if (is_null($amount)) {
            return '-';
        }

        $outputDecimals = (! is_null($numDecimals) ? $numDecimals : $this->decimals);
        $number = BigDecimal::of(10)->power($this->decimals);
        $amount = BigDecimal::of($amount)->dividedBy($number, $this->decimals);
        $number = $amount->toScale($outputDecimals, RoundingMode::DOWN);

        if ($this->decimals === 0 || $amount->getScale() === 0) {
            return number_format((string) $amount->toBigInteger(), ($numDecimals ?: 0), $decimalsSeparator, $thousandsSeparator);
        }

        if ($number->isGreaterThan(BigDecimal::of(1))) {
            $number = number_format($number->toFloat(), $outputDecimals, $decimalsSeparator, $thousandsSeparator);
        }

        return rtrim(rtrim((string) $number, '0'), '.');
    }

    public function getDisplayUsdAmount($amount)
    {
        $amount = $this->getDisplayAmount($amount);
        $amount = (float) preg_replace('/[^\d.]/', '', $amount);
        $price = $this->usd_price;

        return $amount * $price;
    }

    public function getRawJsonAttribute()
    {
        try {
            $znn = App::make(ZenonSdk::class);

            return $znn->token->getByZts($this->token_standard)['data'];
        } catch (\Exception $exception) {
            return null;
        }
    }
}
