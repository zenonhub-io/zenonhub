<?php

declare(strict_types=1);

namespace App\Domains\Nom\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class BridgeNetworkToken extends Pivot
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
    protected $table = 'nom_bridge_network_tokens';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
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

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'metadata' => 'array',
            'is_bridgeable' => 'boolean',
            'is_redeemable' => 'boolean',
            'is_owned' => 'boolean',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    //
    // Methods

    public static function findByTokenAddress(int $networkId, string $address): BridgeNetworkToken
    {
        return static::whereRelation('network', 'id', $networkId)
            ->where('token_address', $address)
            ->sole();
    }

    //
    // Relations

    public function network(): BelongsTo
    {
        return $this->belongsTo(BridgeNetwork::class);
    }

    public function token(): BelongsTo
    {
        return $this->belongsTo(Token::class);
    }
}
