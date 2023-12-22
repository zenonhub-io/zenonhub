<?php

namespace App\Models\Nom;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class BridgeNetwork extends Model
{
    use SoftDeletes;

    protected $table = 'nom_bridge_networks';

    public $timestamps = false;

    protected $fillable = [
        'chain_id',
        'chain_identifier',
        'network_class',
        'name',
        'contract_address',
        'meta_data',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $casts = [
        'meta_data' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    //
    // Relations

    public function chain(): BelongsTo
    {
        return $this->belongsTo(Chain::class);
    }

    public function tokens(): HasMany
    {
        return $this->hasMany(BridgeNetworkToken::class, 'bridge_network_id', 'id');
    }

    public static function findByNetworkChain(int $networkClass, int $chainId): BridgeNetwork
    {
        return static::where('network_class', $networkClass)
            ->where('chain_identifier', $chainId)
            ->sole();
    }
}
