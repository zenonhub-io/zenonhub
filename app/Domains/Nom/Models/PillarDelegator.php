<?php

declare(strict_types=1);

namespace App\Domains\Nom\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PillarDelegator extends Model
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
    protected $table = 'nom_pillar_delegators';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'chain_id',
        'pillar_id',
        'account_id',
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

    public function pillar(): BelongsTo
    {
        return $this->belongsTo(Pillar::class);
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    //
    // Scopes

    public function scopeIsActive($query)
    {
        return $query->whereNull('ended_at');
    }

    public function scopeWithBalance($query)
    {
        return $query->whereRelation('account', 'znn_balance', '>', '0');
    }

    //
    // Attributes

    public function getDisplayWeightAttribute(): string
    {
        return $this->account->display_znn_balance;
    }

    public function getDisplayDurationAttribute(): string
    {
        $endDate = $this->ended_at ?: now();
        $duration = $endDate->timestamp - $this->started_at->timestamp;

        return now()->subSeconds($duration)->diffForHumans(['parts' => 2], true);
    }

    public function getDurationInSecondsAttribute(): float
    {
        return $this->started_at->diffInSeconds($this->ended_at ?: now());
    }

    public function getDisplayPercentageShareAttribute(): string
    {
        $znnBalance = $this->account->znn_balance;
        $weight = $this->pillar->weight;

        if ($this->pillar->revoked_at) {
            $weight = $this->pillar->active_delegators->map(fn ($delegator) => $delegator->account->znn_balance)->sum();
        }

        if ($znnBalance && $weight) {
            $percentage = ($znnBalance / $weight) * 100;

            return number_format($percentage, 2);
        }

        return '0';
    }
}
