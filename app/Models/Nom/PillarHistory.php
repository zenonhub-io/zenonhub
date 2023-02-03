<?php

namespace App\Models\Nom;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PillarHistory extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'nom_pillar_histories';

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
        'producer_id',
        'rewards_account_id',
        'give_momentum_reward_percentage',
        'give_delegate_reward_percentage',
        'is_reward_change',
        'updated_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'updated_at' => 'datetime'
    ];


    /*
     * Relations
     */

    public function pillar()
    {
        return $this->belongsTo(Pillar::class, 'pillar_id', 'id');
    }

    public function producer()
    {
        return $this->belongsTo(Account::class, 'producer_id', 'id');
    }

    public function reward_account()
    {
        return $this->belongsTo(Account::class, 'withdraw_id', 'id');
    }
}
