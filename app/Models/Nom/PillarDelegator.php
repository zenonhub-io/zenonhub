<?php

namespace App\Models\Nom;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PillarDelegator extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'nom_pillar_delegators';

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
        'pillar_id',
        'account_id',
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


    /*
     * Relations
     */

    public function pillar()
    {
        return $this->belongsTo(Pillar::class, 'pillar_id', 'id');
    }

    public function account()
    {
        return $this->belongsTo(Account::class, 'account_id', 'id');
    }


    /*
     * Scopes
     */

    public function scopeIsActive($query)
    {
        return $query->whereNull('ended_at');
    }


    /*
     * Attributes
     */

    public function getDisplayWeightAttribute()
    {
        return $this->account->displayZnnBalance();
    }

    public function getListDisplayWeightAttribute()
    {
        return $this->account->displayZnnBalance(2);
    }

    public function getDisplayDurationAttribute()
    {
        $endDate = $this->ended_at ?: now();
        $duration = $endDate->timestamp - $this->started_at->timestamp;
        return now()->subSeconds($duration)->diffForHumans(['parts' => 2], true);
    }

    public function getDurationInSecondsAttribute()
    {
        return $this->started_at->diffInSeconds($this->ended_at ?: now());
    }
}
