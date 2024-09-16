<?php

declare(strict_types=1);

namespace App\Models\Nom;

use App\Enums\Nom\VoteEnum;
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
        'vote',
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
            'vote' => VoteEnum::class,
            'created_at' => 'datetime',
        ];
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
        $query->where('vote', VoteEnum::YES->value);
    }

    public function scopeWhereNoVote($query)
    {
        $query->where('vote', VoteEnum::NO->value);
    }

    public function scopeWhereAbstainVote($query)
    {
        $query->where('vote', VoteEnum::ABSTAIN->value);
    }
}
