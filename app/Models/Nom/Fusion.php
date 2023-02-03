<?php

namespace App\Models\Nom;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Fusion extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'nom_fusions';

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
        'from_account_id',
        'to_account_id',
        'amount',
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


    /*
     * Relations
     */

    public function from_account()
    {
        return $this->belongsTo(Account::class, 'from_account_id', 'id');
    }

    public function to_account()
    {
        return $this->belongsTo(Account::class, 'to_account_id', 'id');
    }


    /*
     * Scopes
     */

    public function scopeIsActive($query)
    {
        return $query->whereNull('ended_at');
    }

    public function scopeWhereHash($query, $hash)
    {
        return $query->where('hash', $hash);
    }


    /*
     * Attributes
     */

    public function getListDisplayAmountAttribute()
    {
        return qsr_token()->getDisplayAmount($this->amount, 2);
    }

    public function getDisplayAmountAttribute()
    {
        return qsr_token()->getDisplayAmount($this->amount);
    }

    public function getDisplayDurationAttribute()
    {
        $duration = now()->timestamp - $this->started_at->timestamp;
        return now()->subSeconds($duration)->diffForHumans(['parts' => 2], true);
    }


    /*
     * Methods
     */

    public function displayAmount($decimals)
    {
        return qsr_token()->getDisplayAmount($this->amount, $decimals);
    }
}
