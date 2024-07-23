<?php

declare(strict_types=1);

namespace App\Domains\Nom\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Plasma extends Model
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
    protected $table = 'nom_plasma';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'chain_id',
        'from_account_id',
        'to_account_id',
        'account_block_id',
        'amount',
        'hash',
        'started_at',
        'ended_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'started_at' => 'datetime',
            'ended_at' => 'datetime',
        ];
    }

    //
    // Relations

    public function chain(): BelongsTo
    {
        return $this->belongsTo(Chain::class);
    }

    public function fromAccount(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    public function toAccount(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    public function accountBlock(): BelongsTo
    {
        return $this->belongsTo(AccountBlock::class);
    }

    //
    // Scopes

    public function scopeWhereActive($query)
    {
        return $query->whereNull('ended_at');
    }

    public function scopeWhereInactive($query)
    {
        return $query->whereNotNull('ended_at');
    }

    public function scopeWhereHash($query, $hash)
    {
        return $query->where('hash', $hash);
    }

    public function scopeInvolvingAccount($query, $account)
    {
        return $query->where(function ($q) use ($account) {
            $q->where('from_account_id', $account->id)
                ->orWhere('to_account_id', $account->id);
        });
    }

    //
    // Attributes

    public function getDisplayAmountAttribute(): string
    {
        return app('qsrToken')->getFormattedAmount($this->amount);
    }

    public function getDisplayDurationAttribute(): string
    {
        $duration = now()->timestamp - $this->started_at->timestamp;

        return now()->subSeconds($duration)->diffForHumans(['parts' => 2], true);
    }
}
