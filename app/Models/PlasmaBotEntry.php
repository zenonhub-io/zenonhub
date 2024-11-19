<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
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
        'address',
        'hash',
        'amount',
        'is_confirmed',
        'expires_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'expires_at' => 'datetime',
    ];

    //
    // Relations

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
                $query->whereNull('expires_at')
                    ->whereHas('account', function ($query2) {
                        $query2->whereRaw('(SELECT MAX(created_at) FROM nom_account_blocks WHERE nom_account_blocks.account_id = accounts.id) < ?', [
                            now()->subDays(30),
                        ]);
                    });
            });
    }

    public function scopeWhereAddress(Builder $query, $address): Builder
    {
        return $query->where('address', $address);
    }
}
