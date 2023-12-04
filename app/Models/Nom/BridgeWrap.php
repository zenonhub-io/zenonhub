<?php

namespace App\Models\Nom;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    //
    // Relations

    public function network(): BelongsTo
    {
        return $this->belongsTo(BridgeNetwork::class, 'bridge_network_id', 'id');
    }

    public function account(): BelongsTo
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
