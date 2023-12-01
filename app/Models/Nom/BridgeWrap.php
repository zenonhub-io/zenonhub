<?php

namespace App\Models\Nom;

use Illuminate\Database\Eloquent\Model;

class BridgeWrap extends Model
{
    protected $table = 'nom_bridge_wraps';

    public $timestamps = false;

    protected $fillable = [
        'bridge_network_id',
        'account_id',
        'token_id',
        'account_block_id',
        'to_address',
        'signature',
        'amount',
        'created_at',
        'updated_at',
    ];
}
