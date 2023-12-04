<?php

namespace App\Models\Nom;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BridgeNetworkToken extends Model
{
    protected $table = 'nom_bridge_network_tokens';

    public $timestamps = false;

    protected $fillable = [
        'bridge_network_id',
        'token_id',
        'token_address',
        'min_amount',
        'fee_percentage',
        'redeem_delay',
        'metadata',
        'is_bridgeable',
        'is_redeemable',
        'is_owned',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'metadata' => 'array',
        'is_bridgeable' => 'boolean',
        'is_redeemable' => 'boolean',
        'is_owned' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    //
    // Relations

    public function network(): BelongsTo
    {
        return $this->belongsTo(BridgeNetwork::class, 'bridge_network_id', 'id');
    }

    public function token(): BelongsTo
    {
        return $this->belongsTo(Token::class, 'token_id', 'id');
    }
}
