<?php

namespace App\Models\Nom;

use DB;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AcceleratorProjectVote extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'nom_accelerator_project_votes';

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
        'accelerator_project_id',
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

    public function project()
    {
        return $this->belongsTo(AcceleratorProject::class, 'accelerator_project_id', 'id');
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
