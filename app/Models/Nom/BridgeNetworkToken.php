<?php

namespace App\Models\Nom;

use Illuminate\Database\Eloquent\Model;

class BridgeNetworkToken extends Model
{
    protected $table = 'nom_bridge_network_tokens';

    public $timestamps = false;

    protected $fillable = [
        'chain_id',
        'network_class',
        'name',
        'contract_address',
        'meta_data',
        'created_at',
        'updated_at',
    ];
}
