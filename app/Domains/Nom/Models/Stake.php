<?php

declare(strict_types=1);

namespace App\Domains\Nom\Models;

use App\Traits\FindByColumnTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Str;

class Stake extends Model
{
    use FindByColumnTrait, HasFactory;

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
    protected $table = 'nom_stakes';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'chain_id',
        'account_id',
        'token_id',
        'account_block_id',
        'amount',
        'duration',
        'hash',
        'started_at',
        'ended_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'started_at' => 'datetime',
            'ended_at' => 'datetime',
        ];
    }

    //
    // Relations

    public function chain(): BelongsTo
    {
        return $this->belongsTo(Chain::class);
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

    public function scopeIsActive($query)
    {
        return $query->whereNull('ended_at');
    }

    public function scopeIsZnn($query)
    {
        return $query->where('token_id', app('znnToken')->id);
    }

    public function scopeIsEthLp($query)
    {
        return $query->where('token_id', lp_eth_token()->id);
    }

    public function scopeIsEnded($query)
    {
        return $query->whereNotNull('ended_at');
    }

    public function scopeWhereHash($query, $hash)
    {
        return $query->where('hash', $hash);
    }

    //
    // Attributes

    public function getDisplayAmountAttribute(): string
    {
        return $this->token->getFormattedAmount($this->amount);
    }

    public function getEndDateAttribute(): Carbon
    {
        return Carbon::parse($this->started_at)->addSeconds($this->duration);
    }

    public function getDisplayDurationAttribute(): string
    {
        $endDate = Carbon::parse($this->started_at->format('Y-m-d H:i:s'))->addSeconds($this->duration);
        $days = $this->started_at->diffInDays($endDate);

        return $days . ' ' . Str::plural('day', $days);
    }

    public function getCurrentDurationAttribute(): string
    {
        $duration = now()->timestamp - $this->started_at->timestamp;

        return now()->subSeconds($duration)->diffForHumans(['parts' => 2], true);
    }
}
