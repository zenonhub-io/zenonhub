<?php

declare(strict_types=1);

namespace App\Models\Nom;

use App\DataTransferObjects\Nom\TokenDTO;
use App\Enums\Nom\EmbeddedContractsEnum;
use App\Models\Favorite;
use App\Models\SocialProfile;
use App\Services\ZenonSdk\ZenonSdk;
use App\Traits\ModelCacheKeyTrait;
use Brick\Math\BigDecimal;
use Brick\Math\RoundingMode;
use Database\Factories\Nom\TokenFactory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Laravel\Scout\Searchable;
use Maize\Markable\Markable;
use Spatie\Sitemap\Contracts\Sitemapable;
use Throwable;

class Token extends Model implements Sitemapable
{
    use HasFactory, Markable, ModelCacheKeyTrait, Searchable;

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
        'initial_supply',
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
            'total_supply' => 'string',
            'max_supply' => 'string',
            'is_burnable' => 'boolean',
            'is_mintable' => 'boolean',
            'is_utility' => 'boolean',
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
        return route('explorer.token.detail', ['zts' => $this->token_standard]);
    }

    /**
     * {@inheritDoc}
     */
    public function toSearchableArray(): array
    {
        return [
            'token_standard' => $this->token_standard,
            'name' => $this->name,
            'symbol' => $this->symbol,
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
        )->withPivot('price', 'timestamp');
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

    public function bridgeUnwraps(): HasMany
    {
        return $this->hasMany(BridgeUnwrap::class);
    }

    public function bridgeWraps(): HasMany
    {
        return $this->hasMany(BridgeWrap::class);
    }

    public function socialProfile(): MorphOne
    {
        return $this->morphOne(SocialProfile::class, 'profileable');
    }

    public function statHistory(): HasMany
    {
        return $this->hasMany(TokenStatHistory::class);
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

    public function scopeWhereUser($query)
    {
        return $query->whereRelation('owner', 'is_embedded_contract', '0');
    }

    //
    // Attributes

    public function getIsNetworkAttribute()
    {
        $this->loadMissing('owner');

        return $this->owner->is_embedded_contract;
    }

    public function getLockedSupplyAttribute(): float
    {
        $totalLocked = 0;

        if ($this->token_standard === app('znnToken')->token_standard) {
            $pillarLockup = Account::firstWhere('address', EmbeddedContractsEnum::PILLAR->value)->znn_balance;
            $sentinelLockup = Account::firstWhere('address', EmbeddedContractsEnum::SENTINEL->value)->znn_balance;
            $stakingLockup = Account::firstWhere('address', EmbeddedContractsEnum::STAKE->value)->znn_balance;
            $totalLocked = ($pillarLockup + $sentinelLockup + $stakingLockup);
        }

        if ($this->token_standard === app('qsrToken')->token_standard) {
            $sentinelLockup = Account::firstWhere('address', EmbeddedContractsEnum::SENTINEL->value)->qsr_balance;
            $plasmaLockup = Account::firstWhere('address', EmbeddedContractsEnum::PLASMA->value)->qsr_balance;
            $totalLocked = ($sentinelLockup + $plasmaLockup);
        }

        if ($this->token_standard === app('znnEthLpToken')?->token_standard) {
            $liquidityAccount = Account::firstWhere('address', EmbeddedContractsEnum::LIQUIDITY->value);
            $totalLocked = $liquidityAccount->tokens()
                ->where('token_id', $this->id)
                ->first()?->pivot->balance;
        }

        return $totalLocked;
    }

    public function getCirculatingSupplyAttribute(): int|float
    {
        return $this->total_supply - $this->locked_supply;
    }

    public function getHasLockedTokensAttribute(): bool
    {
        return in_array($this->token_standard, [
            app('znnToken')->token_standard,
            app('qsrToken')->token_standard,
            app('znnEthLpToken')->token_standard,
        ], true);
    }

    public function getIsFavouriteAttribute(): bool
    {
        if ($user = auth()->user()) {
            return Favorite::has($this, $user);
        }

        return false;
    }

    public function getPriceAttribute(): string
    {
        $existingPrice = $this->prices()
            ->withPivot('price', 'timestamp')
            ->where('is_default', true)
            ->orderByPivot('timestamp', 'desc')
            ->first();

        return $existingPrice ? $existingPrice->pivot->price : '0';
    }

    public function getRawJsonAttribute(): ?TokenDTO
    {
        try {
            return app(ZenonSdk::class)->getByZts($this->token_standard);
        } catch (Throwable $throwable) {
            return null;
        }
    }

    public function getAvatarSvgAttribute()
    {
        $cacheKey = $this->cacheKey('avatar');

        return Cache::rememberForever($cacheKey, fn () => Http::get(config('zenon-hub.avatar_url'), [
            'seed' => $this->token_standard,
        ])->body());
    }

    //
    // Methods

    public function getDisplayAmount(mixed $amount): int|float
    {
        if (is_null($amount)) {
            return 0;
        }

        $number = BigDecimal::of(10)->power($this->decimals);
        $bigDecimal = BigDecimal::of($amount)->dividedBy($number, $this->decimals);
        $number = $bigDecimal->toScale($this->decimals, RoundingMode::DOWN);

        if ($bigDecimal->isGreaterThan(PHP_INT_MAX) || $bigDecimal->isLessThan(PHP_INT_MIN)) {
            return $number->toFloat();
        }

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

        $displayAmount = $this->getDisplayAmount($amount);
        $outputDecimals = $numDecimals ?? $this->decimals;

        $numberFormatted = number_format($displayAmount, $outputDecimals, $decimalsSeparator, $thousandsSeparator);

        if (str_contains($numberFormatted, $decimalsSeparator)) {
            $numberFormatted = rtrim(rtrim($numberFormatted, '0'), $decimalsSeparator);
        }

        return $numberFormatted;
    }

    public function getDisplayUsdAmount($amount): float
    {
        return $this->getDisplayAmount($amount) * $this->usd_price;
    }
}
