<?php

namespace App\Models\Nom;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
        'redeemed_at',
        'transferred_at',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'transferred_at' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    //
    // Relations

    public function network(): BelongsTo
    {
        return $this->belongsTo(BridgeNetwork::class, 'bridge_network_id', 'id');
    }

    public function to_account(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'to_account_id', 'id');
    }

    public function token(): BelongsTo
    {
        return $this->belongsTo(Token::class, 'token_id', 'id');
    }

    public function account_block(): BelongsTo
    {
        return $this->belongsTo(AccountBlock::class, 'account_block_id', 'id');
    }
}
