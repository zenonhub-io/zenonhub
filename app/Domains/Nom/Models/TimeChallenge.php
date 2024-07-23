<?php

declare(strict_types=1);

namespace App\Domains\Nom\Models;

use App\Traits\ModelCacheKeyTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TimeChallenge extends Model
{
    use ModelCacheKeyTrait;

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
    protected $table = 'nom_time_challenges';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'chain_id',
        'contract_method_id',
        'hash',
        'delay',
        'start_height',
        'end_height',
        'is_active',
        'expires_at',
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
            'expires_at' => 'datetime',
            'created_at' => 'datetime',
        ];
    }

    //
    // Relations

    public function chain(): BelongsTo
    {
        return $this->belongsTo(Chain::class);
    }

    public function contractMethod(): BelongsTo
    {
        return $this->belongsTo(ContractMethod::class);
    }

    //
    // Scopes

    public function scopeWhereActive($query)
    {
        return $query->where('is_active', true);
    }

    //
    // Attributes
}
