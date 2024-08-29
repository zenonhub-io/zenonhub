<?php

declare(strict_types=1);

namespace App\Models\Nom;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class Delegation extends Pivot
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
    protected $table = 'nom_delegations';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
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

    public function pillar(): BelongsTo
    {
        return $this->belongsTo(Pillar::class);
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
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
