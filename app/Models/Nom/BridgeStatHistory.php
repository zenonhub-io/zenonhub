<?php

declare(strict_types=1);

namespace App\Models\Nom;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BridgeStatHistory extends Model
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
    protected $table = 'nom_bridge_stat_histories';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'bridge_network_id',
        'token_id',
        'wrap_tx',
        'wrapped_amount',
        'unwrap_tx',
        'unwrapped_amount',
        'affiliate_tx',
        'affiliate_amount',
        'total_volume',
        'total_flow',
        'date',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'wrap_tx' => 'int',
            'wrapped_amount' => 'string',
            'unwrap_tx' => 'int',
            'unwrapped_amount' => 'string',
            'affiliate_tx' => 'int',
            'affiliate_amount' => 'string',
            'total_volume' => 'string',
            'total_flow' => 'string',
            'date' => 'date',
        ];
    }

    //
    // Relations

    public function bridgeNetwork(): BelongsTo
    {
        return $this->belongsTo(BridgeNetwork::class);
    }

    public function token(): BelongsTo
    {
        return $this->belongsTo(Token::class);
    }
}
