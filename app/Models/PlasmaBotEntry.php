<?php

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

    public function scopeIsUnConfirmed(Builder $query): Builder
    {
        return $query->where('is_confirmed', 0);
    }

    //
    // Methods

    public function confirm($hash = null)
    {
        if ($hash) {
            $this->hash = $hash;
        }

        $this->is_confirmed = true;
        $this->save();
    }
}
