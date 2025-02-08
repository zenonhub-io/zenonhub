<?php

declare(strict_types=1);

namespace App\Models\Nom;

use Database\Factories\Nom\BridgeNetworkFactory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class BridgeNetwork extends Model
{
    use HasFactory, SoftDeletes;

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
        'explorer_url',
        'explorer_tx_link',
        'explorer_address_link',
        'total_znn_held',
        'total_znn_wrapped',
        'total_znn_unwrapped',
        'total_qsr_held',
        'total_qsr_wrapped',
        'total_qsr_unwrapped',
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

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory(): Factory
    {
        return BridgeNetworkFactory::new();
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

    public function wraps(): HasMany
    {
        return $this->hasMany(BridgeWrap::class);
    }

    public function unwraps(): HasMany
    {
        return $this->hasMany(BridgeUnwrap::class);
    }

    //
    // Attributes

}
