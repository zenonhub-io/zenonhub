<?php

namespace App\Models\Nom;

use Carbon\Carbon;
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

    public function bridge_network(): BelongsTo
    {
        return $this->belongsTo(BridgeNetwork::class, 'bridge_network_id', 'id');
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'account_id', 'id');
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
    // Scopes

    public function scopeCreatedLast($query, ?string $limit)
    {
        if ($limit) {
            return $query->where('created_at', '>=', $limit);
        }

        return $query;
    }

    public function scopeCreatedBetweenDates($query, array $dates)
    {
        $start = ($dates[0] instanceof Carbon) ? $dates[0] : Carbon::parse($dates[0]);
        $end = ($dates[1] instanceof Carbon) ? $dates[1] : Carbon::parse($dates[1]);

        return $query->whereBetween('created_at', [
            $start->startOfDay(),
            $end->endOfDay(),
        ]);
    }

    //
    // Attributes

    public function getDisplayAmountAttribute(): string
    {
        return $this->token->getDisplayAmount($this->amount);
    }

    public function getToAddressLinkAttribute(): ?string
    {
        return $this->bridge_network->explorer_url.'address/'.$this->to_address;
    }
}
