<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Nom\Account;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class PlasmaBotEntry extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'account_id',
        'hash',
        'amount',
        'is_confirmed',
        'should_expire',
        'expires_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'expires_at' => 'datetime',
        'is_confirmed' => 'bool',
        'should_expire' => 'bool',
    ];

    //
    // Relations

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    //
    // Scopes

    public function scopeWhereUnConfirmed(Builder $query): Builder
    {
        return $query->where('is_confirmed', 0);
    }

    public function scopeWhereConfirmed(Builder $query): Builder
    {
        return $query->where('is_confirmed', '1');
    }

    public function scopeWhereExpired(Builder $query): Builder
    {
        return $query->where('expires_at', '<', now())
            ->orWhere(function ($query) {
                $query->where('should_expire', true);
                $query->whereNull('expires_at')
                    ->whereHas('account', function ($query2) {
                        $query2->whereRaw('(
                            SELECT MAX(created_at)
                            FROM nom_account_blocks
                            WHERE nom_account_blocks.account_id = plasma_bot_entries.account_id
                        ) < ?', [
                            now()->subDays(30),
                        ]);
                    });
            });
    }
}
