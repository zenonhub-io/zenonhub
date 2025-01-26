<?php

declare(strict_types=1);

namespace App\Models\Nom;

use App\Enums\Nom\AccountRewardTypesEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AccountReward extends Model
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
    protected $table = 'nom_account_rewards';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'chain_id',
        'account_block_id',
        'account_id',
        'token_id',
        'type',
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
            'type' => AccountRewardTypesEnum::class,
        ];
    }

    //
    // Relations

    public function chain(): BelongsTo
    {
        return $this->belongsTo(Chain::class);
    }

    public function accountBlock(): BelongsTo
    {
        return $this->belongsTo(AccountBlock::class);
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    public function token(): BelongsTo
    {
        return $this->belongsTo(Token::class);
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
