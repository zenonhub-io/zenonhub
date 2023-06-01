<?php

namespace App\Models\Nom;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class AcceleratorVote extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'nom_accelerator_votes';

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
        'owner_id',
        'pillar_id',
        'votable_id',
        'votable_type',
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

    //
    // Relations

    public function votable(): MorphTo
    {
        return $this->morphTo();
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'owner_id', 'id');
    }

    public function pillar(): BelongsTo
    {
        return $this->belongsTo(Pillar::class, 'pillar_id', 'id');
    }

    //
    // Scopes

    public function scopeWhereYesVote($query)
    {
        $query->where('is_yes', '1');
    }

    public function scopeWhereNoVote($query)
    {
        $query->where('is_yes', '1');
    }

    public function scopeWhereAbstainVote($query)
    {
        $query->where('is_yes', '1');
    }
}
