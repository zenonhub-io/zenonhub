<?php

namespace App\Models\Nom;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TokenBurn extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'nom_token_burns';

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
        'token_id',
        'account_id',
        'account_block_id',
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

    public function chain(): BelongsTo
    {
        return $this->belongsTo(Chain::class, 'chain_id', 'id');
    }

    public function token(): BelongsTo
    {
        return $this->belongsTo(Token::class, 'token_id', 'id');
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'account_id', 'id');
    }

    public function account_block(): BelongsTo
    {
        return $this->belongsTo(AccountBlock::class, 'account_block_id', 'id');
    }

    //
    // Scopes

    //
    // Attributes

    public function getDisplayAmountAttribute()
    {
        return $this->token->getDisplayAmount($this->amount);
    }
}
