<?php

namespace App\Models\Nom;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BridgeAdmin extends Model
{
    protected $table = 'nom_bridge_admins';

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
}
