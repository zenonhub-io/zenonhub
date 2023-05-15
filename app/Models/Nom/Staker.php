<?php

namespace App\Models\Nom;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Staker extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'nom_stakers';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    public $fillable = [
        'chain_id',
        'account_id',
        'amount',
        'duration',
        'hash',
        'started_at',
        'ended_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
    ];

    //
    // Relations

    public function chain(): BelongsTo
    {
        return $this->belongsTo(Chain::class, 'chain_id', 'id');
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

    public function scopeWhereHash($query, $hash)
    {
        return $query->where('hash', $hash);
    }

    //
    // Attributes

    public function getDisplayAmountAttribute()
    {
        return znn_token()->getDisplayAmount($this->amount);
    }

    public function getEndDateAttribute()
    {
        return Carbon::parse($this->started_at)->addSeconds($this->duration);
    }

    public function getDisplayDurationAttribute()
    {
        $endDate = \Carbon\Carbon::parse($this->started_at->format('Y-m-d H:i:s'))->addSeconds($this->duration);
        $days = $this->started_at->diffInDays($endDate);

        return $days.' '.\Str::plural('day', $days);
    }

    public function getCurrentDurationAttribute()
    {
        $duration = now()->timestamp - $this->started_at->timestamp;

        return now()->subSeconds($duration)->diffForHumans(['parts' => 2], true);
    }

    //
    // Methods

    public function displayAmount($decimals = null)
    {
        return znn_token()->getDisplayAmount($this->amount, $decimals);
    }
}
