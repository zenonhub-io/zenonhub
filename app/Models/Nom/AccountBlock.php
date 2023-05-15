<?php

namespace App\Models\Nom;

use App;
use Cache;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class AccountBlock extends Model
{
    use HasFactory;

    public const TYPE_GENESIS = 1;

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
        'chain_id',
        'account_id',
        'to_account_id',
        'momentum_id',
        'parent_block_id',
        'paired_account_block_id',
        'token_id',
        'contract_method_id',
        'version',
        'block_type',
        'height',
        'amount',
        'fused_plasma',
        'base_plasma',
        'used_plasma',
        'difficulty',
        'hash',
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
        return $this->belongsTo(Chain::class);
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'account_id', 'id');
    }

    public function to_account(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'to_account_id', 'id');
    }

    public function momentum(): BelongsTo
    {
        return $this->belongsTo(Momentum::class);
    }

    public function paired_account_block(): BelongsTo
    {
        return $this->belongsTo(AccountBlock::class, 'paired_account_block_id', 'id');
    }

    public function descendants(): HasMany
    {
        return $this->hasMany(AccountBlock::class, 'parent_block_id', 'id');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(AccountBlock::class, 'parent_block_id', 'id');
    }

    public function token(): HasOne
    {
        return $this->hasOne(Token::class, 'id', 'token_id');
    }

    public function contract_method(): HasOne
    {
        return $this->hasOne(ContractMethod::class, 'id', 'contract_method_id');
    }

    public function data(): HasOne
    {
        return $this->hasOne(AccountBlockData::class, 'account_block_id', 'id');
    }

    //
    // Scopes

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
        }

        return $query->whereHas('momentum', function ($q) use ($start) {
            $q->where('height', $start);
        });
    }

    public function scopeInvolvingAccount($query, $account)
    {
        return $query->where(function ($q) use ($account) {
            $q->where('account_id', $account->id)
                ->orWhere('to_account_id', $account->id);
        });
    }

    public function scopeIsReceived($query)
    {
        return $query->whereNotNull('paired_account_block_id');
    }

    public function scopeIsUnreceived($query)
    {
        return $query->whereNull('paired_account_block_id');
    }

    public function scopeNotToEmpty($query)
    {
        return $query->where('to_account_id', '!=', '1');
    }

    //
    // Attributes

    public function getDisplayActualTypeAttribute()
    {
        if ($this->block_type === self::TYPE_GENESIS) {
            return 'Genesis';
        }

        if ($this->block_type === self::TYPE_SEND) {
            return 'Send';
        }

        if ($this->block_type === self::TYPE_RECEIVE) {
            return 'Receive';
        }

        if ($this->block_type === self::TYPE_CONTRACT_SEND) {
            return 'Contract Send';
        }

        if ($this->block_type === self::TYPE_CONTRACT_RECEIVE) {
            return 'Contract Receive';
        }

        return '-';
    }

    public function getDisplayTypeAttribute()
    {
        if ($this->contract_method) {
            return $this->contract_method->name;
        }

        if ($this->amount > 0) {
            return 'Transfer';
        }

        return $this->getDisplayActualTypeAttribute();
    }

    public function getDisplayAmountAttribute()
    {
        return $this->token?->getDisplayAmount($this->amount);
    }

    public function getDisplayHeightAttribute()
    {
        return number_format($this->height);
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
        return Cache::remember("block-{$this->id}", 60 * 5, function () {
            try {
                $znn = App::make('zenon.api');

                return $znn->ledger->getAccountBlockByHash($this->hash)['data'];
            } catch (\Exception $exception) {
                return null;
            }
        });
    }

    public function getIsUnReceivedAttribute()
    {
        return ! $this->paired_account_block_id;
    }

    //
    // Methods

    public static function findByHash($hash)
    {
        return static::where('hash', $hash)->first();
    }

    public function displayAmount($decimals = null)
    {
        return $this->token?->getDisplayAmount($this->amount, $decimals);
    }
}
