<?php

namespace App\Models\Nom;

use App;
use Cache;
use DigitalSloth\ZnnPhp\Utilities as ZnnUtilities;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccountBlock extends Model
{
    use HasFactory;

    public const TYPE_SEND = 2;
    public const TYPE_RECEIVE = 3;
    public const TYPE_CONTRACT_SEND = 4;
    public const TYPE_CONTRACT_RECEIVE = 5;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'nom_account_blocks';

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
        'account_id',
        'to_account_id',
        'momentum_id',
        'parent_block_id',
        'paired_account_block_id',
        'token_id',
        'version',
        'chain_identifier',
        'block_type',
        'height',
        'amount',
        'fused_plasma',
        'base_plasma',
        'used_plasma',
        'difficulty',
        'hash',
        'nonce',
        'public_key',
        'signature',
        'created_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'created_at' => 'datetime'
    ];


    /*
     * Relations
     */

    public function account()
    {
        return $this->belongsTo(Account::class, 'account_id', 'id');
    }

    public function to_account()
    {
        return $this->belongsTo(Account::class, 'to_account_id', 'id');
    }

    public function momentum()
    {
        return $this->belongsTo(Momentum::class);
    }

    public function paired_account_block()
    {
        return $this->belongsTo(AccountBlock::class, 'paired_account_block_id', 'id');
    }

    public function descendants()
    {
        return $this->hasMany(AccountBlock::class, 'parent_block_id', 'id');
    }

    public function parent()
    {
        return $this->belongsTo(AccountBlock::class, 'parent_block_id', 'id');
    }

    public function token()
    {
        return $this->hasOne(Token::class, 'id', 'token_id');
    }

    public function data()
    {
        return $this->hasOne(AccountBlockData::class, 'account_block_id', 'id');
    }

    /*
     * Scopes
     */

    public function scopeWhereListSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('hash', $search);
        });
    }

    public function scopeBetweenMomentums($query, $start, $end = false)
    {
        if ($end) {
            return $query->whereHas('momentum', function ($q) use ($start, $end) {
                $q->where('height', '>=', $start)
                    ->where('height', '<', $end);
            });
        } else {
            return $query->whereHas('momentum', function ($q) use ($start)  {
                $q->where('height', $start);
            });
        }
    }


    /*
     * Attributes
     */

    public function getDisplayTypeAttribute()
    {
        if ($this->block_type === self::TYPE_SEND) {
            return 'Send';
        } elseif ($this->block_type === self::TYPE_RECEIVE) {
            return 'Receive';
        } elseif ($this->block_type === self::TYPE_CONTRACT_SEND) {
            return 'Contract Send';
        } elseif ($this->block_type === self::TYPE_CONTRACT_RECEIVE) {
            return 'Contract Receive';
        }

        return '-';
    }

    public function getListDisplayAmountAttribute()
    {
        return $this->token?->getDisplayAmount($this->amount, 2);
    }

    public function getDisplayAmountAttribute()
    {
        return $this->token?->getDisplayAmount($this->amount);
    }

    public function getDisplayHeightAttribute()
    {
        return number_format($this->height);
    }

    public function getContractMethodAttribute()
    {
        return $this->data?->contract_method;
    }

    public function getDecodedPublicKeyAttribute()
    {
        return ZnnUtilities::decodeData($this->public_key);
    }

    public function getDecodedSignatureAttribute()
    {
        return ZnnUtilities::decodeData($this->signature);
    }

    public function getRawDataAttribute()
    {
        return Cache::rememberForever("account-block-{$this->id}", function () {
            $znn = App::make('zenon.api');
            return $znn->ledger->getAccountBlockByHash($this->hash)['data'];
        });
    }

    public function getNextBlockAttribute()
    {
        return self::where('account_id', $this->account_id)
            ->where('height', ($this->height + 1))
            ->first();
    }

    public function getPreviousBlockAttribute()
    {
        return self::where('account_id', $this->account_id)
            ->where('height', ($this->height - 1))
            ->first();
    }

    public function getRawJsonAttribute()
    {
        return Cache::remember("block-{$this->id}", 60*5, function () {
            $znn = App::make('zenon.api');
            return $znn->ledger->getAccountBlockByHash($this->hash)['data'];
        });
    }


    /*
     * Methods
     */

    public static function findByHash($hash)
    {
        return static::where('hash', $hash)->first();
    }

    public function displayAmount($decimals = null)
    {
        return $this->token?->getDisplayAmount($this->amount, $decimals);
    }
}
