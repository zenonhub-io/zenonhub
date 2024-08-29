<?php

declare(strict_types=1);

namespace App\Models\Nom;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BridgeWrap extends Model
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
    protected $table = 'nom_bridge_wraps';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
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

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    //
    // Relations

    public function bridgeNetwork(): BelongsTo
    {
        return $this->belongsTo(BridgeNetwork::class);
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    public function token(): BelongsTo
    {
        return $this->belongsTo(Token::class);
    }

    public function accountBlock(): BelongsTo
    {
        return $this->belongsTo(AccountBlock::class);
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
        return $this->token->getFormattedAmount($this->amount);
    }

    public function getToAddressLinkAttribute(): string
    {
        return $this->bridgeNetwork->explorer_url . 'address/' . $this->to_address;
    }
}
