<?php

declare(strict_types=1);

namespace App\Domains\Nom\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PillarStatHistory extends Model
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
    protected $table = 'nom_pillar_stat_histories';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'pillar_id',
        'rank',
        'weight',
        'momentum_rewards',
        'delegate_rewards',
        'total_delegators',
        'date',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'date' => 'date',
        ];
    }

    //
    // Relations

    public function pillar(): BelongsTo
    {
        return $this->belongsTo(Pillar::class);
    }
}
