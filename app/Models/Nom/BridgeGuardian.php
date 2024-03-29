<?php

namespace App\Models\Nom;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BridgeGuardian extends Model
{
    protected $table = 'nom_bridge_guardians';

    public $timestamps = false;

    protected $fillable = [
        'account_id',
        'nominated_at',
        'accepted_at',
        'revoked_at',
    ];

    protected $casts = [
        'meta_data' => 'array',
        'nominated_at' => 'datetime',
        'accepted_at' => 'datetime',
        'revoked_at' => 'datetime',
    ];

    //
    // Relations

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'account_id', 'id');
    }

    //
    // Scopes

    public function scopeAllActive($query)
    {
        return $query->whereNull('revoked_at');
    }
}
