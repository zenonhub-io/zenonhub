<?php

namespace App\Models\Nom;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AccountReward extends Model
{
    use HasFactory;

    public const TYPE_DELEGATE = 1;

    public const TYPE_STAKE = 2;

    public const TYPE_PILLAR = 3;

    public const TYPE_SENTINEL = 4;

    public const TYPE_LIQUIDITY = 5;

    public const TYPE_LIQUIDITY_PROGRAM = 6;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'nom_account_rewards';

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
        'token_id',
        'type',
        'amount',
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

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'account_id', 'id');
    }

    public function token(): BelongsTo
    {
        return $this->belongsTo(Token::class, 'token_id', 'id');
    }

    //
    // Scopes

    //
    // Attributes

    public function getDisplayAmountAttribute()
    {
        return $this->token->getDisplayAmount($this->amount);
    }

    public function getDisplayTypeAttribute()
    {
        if ($this->type === self::TYPE_DELEGATE) {
            return 'Delegate';
        }

        if ($this->type === self::TYPE_STAKE) {
            return 'Stake';
        }

        if ($this->type === self::TYPE_PILLAR) {
            return 'Pillar';
        }

        if ($this->type === self::TYPE_SENTINEL) {
            return 'Sentinel';
        }

        if ($this->type === self::TYPE_LIQUIDITY) {
            return 'Liquidity';
        }

        if ($this->type === self::TYPE_LIQUIDITY_PROGRAM) {
            return 'Liquidity Program';
        }

        return null;
    }

    //
    // Methods

    public function displayAmount($decimals)
    {
        return $this->token->getDisplayAmount($this->amount, $decimals);
    }
}
