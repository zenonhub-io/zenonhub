<?php

declare(strict_types=1);

namespace App\Domains\Nom\Models;

use Database\Factories\Domains\Nom\TokenMintFactory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TokenMint extends Model
{
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
    protected $table = 'nom_token_mints';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'chain_id',
        'token_id',
        'issuer_id',
        'receiver_id',
        'account_block_id',
        'amount',
        'created_at',
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
        ];
    }

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory(): Factory
    {
        return TokenMintFactory::new();
    }

    //
    // Relations

    public function chain(): BelongsTo
    {
        return $this->belongsTo(Chain::class);
    }

    public function token(): BelongsTo
    {
        return $this->belongsTo(Token::class);
    }

    public function issuer(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    public function receiver(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    public function accountBlock(): BelongsTo
    {
        return $this->belongsTo(AccountBlock::class);
    }

    //
    // Scopes

    //
    // Attributes

    public function getDisplayAmountAttribute(): string
    {
        return $this->token->getFormattedAmount($this->amount);
    }
}
