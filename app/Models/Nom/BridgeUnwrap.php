<?php

declare(strict_types=1);

namespace App\Models\Nom;

use Carbon\Carbon;
use Database\Factories\Nom\BridgeUnwrapFactory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Http;

class BridgeUnwrap extends Model
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
    protected $table = 'nom_bridge_unwraps';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'bridge_network_id',
        'to_account_id',
        'token_id',
        'account_block_id',
        'from_address',
        'transaction_hash',
        'log_index',
        'signature',
        'amount',
        'redeemed_at',
        'created_at',
        'updated_at',
        'revoked_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'redeemed_at' => 'datetime',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'revoked_at' => 'datetime',
        ];
    }

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory(): Factory
    {
        return BridgeUnwrapFactory::new();
    }

    //
    // Methods

    public static function findByTxHashLog(string|int $hash, string|int $log): ?BridgeUnwrap
    {
        return static::where('transaction_hash', $hash)
            ->where('log_index', $log)
            ->first();
    }

    //
    // Scopes

    public static function scopeWhereTxHashLog($query, string $hash, int $log)
    {
        return $query->where('transaction_hash', $hash)
            ->where('log_index', $log);
    }

    public static function scopeWhereUnredeemed($query)
    {
        return $query->whereNull('redeemed_at');
    }

    public function scopeWhereAffiliateReward($query)
    {
        return $query->where('log_index', '>=', '4000000000');
    }

    public function scopeWhereNotAffiliateReward($query)
    {
        return $query->where('log_index', '<', '4000000000');
    }

    //
    // Relations

    public function bridgeNetwork(): BelongsTo
    {
        return $this->belongsTo(BridgeNetwork::class);
    }

    public function toAccount(): BelongsTo
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

    public function getIsAffiliateRewardAttribute(): bool
    {
        return $this->log_index > 4000000000;
    }

    public function getFromAddressLinkAttribute(): string
    {
        return $this->bridgeNetwork->explorer_url . '/' . $this->bridgeNetwork->explorer_address_link . '/' . $this->from_address;
    }

    public function getTxHashLinkAttribute(): string
    {
        return $this->bridgeNetwork->explorer_url . '/' . $this->bridgeNetwork->explorer_tx_link . '/0x' . $this->transaction_hash;
    }

    public function getDisplayAmountAttribute(): string
    {
        return $this->token?->getFormattedAmount($this->amount);
    }

    public function setFromAddress(): void
    {
        if ($this->from_address) {
            return;
        }

        $this->from_address = Http::get('https://api.etherscan.io/api', [
            'module' => 'proxy',
            'action' => 'eth_getTransactionByHash',
            'txhash' => '0x' . $this->transaction_hash,
            'apikey' => config('services.etherscan.api_key'),
        ])->json('result.from');

        $this->save();
    }
}
