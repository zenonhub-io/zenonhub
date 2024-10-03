<?php

declare(strict_types=1);

namespace App\Models\Nom;

use Database\Factories\Nom\BridgeNetworkTokenFactory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class BridgeNetworkToken extends Pivot
{
    use HasFactory;

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

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory(): Factory
    {
        return BridgeNetworkTokenFactory::new();
    }

    //
    // Methods

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
