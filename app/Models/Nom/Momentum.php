<?php

namespace App\Models\Nom;

use App;
use App\Models\Markable\Favorite;
use Cache;
use DigitalSloth\ZnnPhp\Utilities as ZnnUtilities;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Maize\Markable\Markable;

class Momentum extends Model
{
    use HasFactory, Markable;

    protected static array $marks = [
        Favorite::class,
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'nom_momentums';

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
        'producer_account_id',
        'producer_pillar_id',
        'version',
        'height',
        'hash',
        'data',
        'created_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'created_at' => 'datetime',
    ];

    //
    // Relations

    public function chain(): BelongsTo
    {
        return $this->belongsTo(Chain::class, 'chain_id', 'id');
    }

    public function producer_account(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'producer_account_id', 'id');
    }

    public function producer_pillar(): BelongsTo
    {
        return $this->belongsTo(Pillar::class, 'producer_pillar_id', 'id');
    }

    public function account_blocks(): HasMany
    {
        return $this->hasMany(AccountBlock::class, 'momentum_id', 'id');
    }

    //
    // Scopes

    public function scopeWhereListSearch($query, $search)
    {
        $query->where('height', '>', 0);

        if (is_numeric($search)) {
            return $query->where('height', $search);
        } else {
            return $query->where('hash', $search);
        }
    }

    //
    // Attributes

    public function getDecodedDataAttribute()
    {
        $data = base64_decode($this->data);

        return ZnnUtilities::toHex($data);
    }

    public function getDisplayHeightAttribute()
    {
        return number_format($this->height);
    }

    public function getNextMomentumAttribute()
    {
        return self::where('height', ($this->height + 1))->first();
    }

    public function getPreviousMomentumAttribute()
    {
        $previous = ($this->height - 1);

        if ($previous > 0) {
            return self::where('height', $previous)->first();
        }

        return false;
    }

    public function getRawJsonAttribute()
    {
        return Cache::rememberForever("momentum-{$this->id}", function () {
            try {
                $znn = App::make('zenon.api');

                return $znn->ledger->getMomentumByHash($this->hash)['data'];
            } catch (\Exception $exception) {
                return null;
            }
        });
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

    public static function findByHash($hash)
    {
        return static::where('hash', $hash)->first();
    }
}
