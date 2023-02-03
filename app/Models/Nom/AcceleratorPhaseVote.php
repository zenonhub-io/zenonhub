<?php

namespace App\Models\Nom;

use DB;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AcceleratorPhaseVote extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'nom_accelerator_phase_votes';

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
        'accelerator_phase_id',
        'owner_id',
        'pillar_id',
        'is_yes',
        'is_no',
        'is_abstain',
        'created_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'created_at' => 'datetime',
    ];


    /*
     * Relations
     */

    public function phase()
    {
        return $this->belongsTo(AcceleratorPhase::class, 'accelerator_phase_id', 'id');
    }

    public function owner()
    {
        return $this->belongsTo(Account::class, 'owner_id', 'id');
    }

    public function oillar()
    {
        return $this->belongsTo(Pillar::class, 'pillar_id', 'id');
    }
}
