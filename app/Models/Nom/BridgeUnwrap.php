<?php

namespace App\Models\Nom;

use Illuminate\Database\Eloquent\Model;

class BridgeUnwrap extends Model
{
    protected $table = 'nom_bridge_unwraps';

    public $timestamps = false;

    protected $fillable = [
        'bridge_network_id',
        'to_account_id',
        'token_id',
        'account_block_id',
        'transaction_hash',
        'log_index',
        'token_address',
        'signature',
        'amount',
        'is_redeemed',
        'is_transferred',
        'created_at',
        'updated_at',
    ];
}
