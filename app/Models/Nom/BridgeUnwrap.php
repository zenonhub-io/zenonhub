<?php

namespace App\Models\Nom;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Http;

class BridgeUnwrap extends Model
{
    protected $table = 'nom_bridge_unwraps';

    public $timestamps = false;

    protected $fillable = [
        'bridge_network_id',
        'bridge_network_token_id',
        'to_account_id',
        'token_id',
        'account_block_id',
        'from_address',
        'transaction_hash',
        'log_index',
        'token_address',
        'signature',
        'amount',
        'redeemed_at',
        'created_at',
        'updated_at',
        'revoked_at',
    ];

    protected $casts = [
        'redeemed_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'revoked_at' => 'datetime',
    ];

    //
    // Relations

    public function bridge_network(): BelongsTo
    {
        return $this->belongsTo(BridgeNetwork::class, 'bridge_network_id', 'id');
    }

    public function bridge_network_token(): BelongsTo
    {
        return $this->belongsTo(BridgeNetworkToken::class, 'bridge_network_token_id', 'id');
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

    //
    // Attributes

    public function getIsAffiliateRewardAttribute(): bool
    {
        return $this->log_index > 4000000000;
    }

    public function getFromAddressLinkAttribute(): string
    {
        if ($this->bridge_network->name === 'Ethereum') {
            return 'https://etherscan.io/address/'.$this->from_address;
        }
    }

    public function getTxHashLinkAttribute(): string
    {
        if ($this->bridge_network->name === 'Ethereum') {
            return 'https://etherscan.io/tx/0x'.$this->transaction_hash;
        }
    }

    public function getDisplayAmountAttribute(): string
    {
        return $this->token?->getDisplayAmount($this->amount);
    }

    public function setFromAddress(): void
    {
        if ($this->from_address) {
            return;
        }

        $this->from_address = Http::get('https://api.etherscan.io/api', [
            'module' => 'proxy',
            'action' => 'eth_getTransactionByHash',
            'txhash' => '0x'.$this->transaction_hash,
            'apikey' => config('etherscan.api_key'),
        ])->json('result.from');

        $this->save();
    }
}
