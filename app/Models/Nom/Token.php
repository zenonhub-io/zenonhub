<?php

namespace App\Models\Nom;

use App;
use App\Models\Markable\Favorite;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Maize\Markable\Markable;
use Spatie\Sitemap\Contracts\Sitemapable;

class Token extends Model implements Sitemapable
{
    use HasFactory, Markable;

    public const ZTS_EMPTY = 'zts1qqqqqqqqqqqqqqqqtq587y';

    public const ZTS_ZNN = 'zts1znnxxxxxxxxxxxxx9z4ulx';

    public const ZTS_QSR = 'zts1qsrxxxxxxxxxxxxxmrhjll';

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

    public function getDisplayAmount($amount, $numDecimals = null)
    {
        if (is_null($amount)) {
            return '-';
        }

        $decimals = str_pad('1', $this->decimals + 1, '0');
        $amount = ($amount / (int) $decimals);

        // If number has 0 decimal places return
        if (floor($amount) == $amount) {
            return number_format($amount, ($numDecimals ?: 0));
        }

        // Format to given number of decimal places or default for token
        $number = number_format($amount, (! is_null($numDecimals) ? $numDecimals : $this->decimals));

        // Remove any unneeded 0s
        return rtrim(rtrim($number, 0), '.');
    }

    public function getRawJsonAttribute()
    {
        try {
            $znn = App::make('zenon.api');

            return $znn->token->getByZts($this->token_standard)['data'];
        } catch (\Exception $exception) {
            return null;
        }
    }

    public function getTotalSupplyAttribute()
    {
        return $this->raw_json?->totalSupply;
    }
}
