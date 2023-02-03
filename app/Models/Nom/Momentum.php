<?php

namespace App\Models\Nom;

use App;
use Cache;
use DigitalSloth\ZnnPhp\Utilities as ZnnUtilities;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Momentum extends Model
{
    use HasFactory;

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
        'producer_account_id',
        'producer_pillar_id',
        'version',
        'chain_identifier',
        'height',
        'hash',
        'public_key',
        'signature',
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


    /*
     * Relations
     */

    public function producer_account()
    {
        return $this->belongsTo(Account::class, 'producer_account_id', 'id');
    }

    public function producer_pillar()
    {
        return $this->belongsTo(Pillar::class, 'producer_pillar_id', 'id');
    }

    public function account_blocks()
    {
        return $this->hasMany(AccountBlock::class, 'momentum_id', 'id');
    }


    /*
     * Scopes
     */

    public function scopeWhereListSearch($query, $search)
    {
        $query->where('height', '>', 0);

        if (is_numeric($search)) {
            return $query->where('height', $search);
        } else {
            return $query->where('hash', $search);
        }
    }

    /*
     * Attributes
     */

    public function getDecodedPublicKeyAttribute()
    {
        return ZnnUtilities::decodeData($this->public_key);
    }

    public function getDecodedSignatureAttribute()
    {
        return ZnnUtilities::decodeData($this->signature);
    }

    public function getDecodedDataAttribute()
    {
        return ZnnUtilities::decodeData($this->data);
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
            $znn = App::make('zenon.api');
            return $znn->ledger->getMomentumByHash($this->hash)['data'];
        });
    }


    /*
     * Methods
     */

    public static function findByHash($hash)
    {
        return static::where('hash', $hash)->first();
    }
}
