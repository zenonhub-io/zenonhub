<?php

declare(strict_types=1);

namespace App\Models\Nom;

use App\Traits\ModelCacheKeyTrait;
use Database\Factories\TimeChallengeFactory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TimeChallenge extends Model
{
    use HasFactory, ModelCacheKeyTrait;

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
            'is_active' => 'boolean',
            'expires_at' => 'datetime',
            'created_at' => 'datetime',
        ];
    }

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory(): Factory
    {
        return TimeChallengeFactory::new();
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
