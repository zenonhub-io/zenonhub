<?php

declare(strict_types=1);

namespace App\Domains\Nom\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class BridgeNetwork extends Model
{
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'nom_bridge_networks';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'chain_id',
        'chain_identifier',
        'network_class',
        'name',
        'contract_address',
        'meta_data',
        'created_at',
        'updated_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'meta_data' => 'array',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }

    //
    // Methods

    public static function findByNetworkChain(string $networkClass, string $chainId): ?BridgeNetwork
    {
        return static::where('network_class', $networkClass)
            ->where('chain_identifier', $chainId)
            ->first();
    }

    //
    // Relations

    public function chain(): BelongsTo
    {
        return $this->belongsTo(Chain::class);
    }

    public function tokens(): BelongsToMany
    {
        return $this->belongsToMany(Token::class, 'nom_bridge_network_tokens')
            ->using(BridgeNetworkToken::class)
            ->withPivot(
                'token_address',
                'min_amount',
                'fee_percentage',
                'redeem_delay',
                'metadata',
                'is_bridgeable',
                'is_redeemable',
                'is_owned',
                'created_at',
                'updated_at'
            );
    }

    //
    // Attributes

    public function getExplorerUrlAttribute(): ?string
    {
        if ($this->name === 'Ethereum') {
            return 'https://etherscan.io/';
        }

        return null;
    }
}
