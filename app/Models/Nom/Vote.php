<?php

declare(strict_types=1);

namespace App\Models\Nom;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Vote extends Model
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
    protected $table = 'nom_votes';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
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
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
        ];
    }

    //
    // Helpers

    public static function isVoteType(string $type, string $vote): bool
    {
        if ($type === 'yes' && $vote === '0') {
            return true;
        }

        if ($type === 'no' && $vote === '1') {
            return true;
        }

        if ($type === 'abstain' && $vote === '2') {
            return true;
        }

        return false;
    }

    //
    // Relations

    public function votable(): MorphTo
    {
        return $this->morphTo();
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    public function pillar(): BelongsTo
    {
        return $this->belongsTo(Pillar::class);
    }

    //
    // Scopes

    public function scopeWhereYesVote($query)
    {
        $query->where('is_yes', '1');
    }

    public function scopeWhereNoVote($query)
    {
        $query->where('is_no', '1');
    }

    public function scopeWhereAbstainVote($query)
    {
        $query->where('is_abstain', '1');
    }
}
