<?php

declare(strict_types=1);

namespace App\Domains\Nom\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Orchestrator extends Model
{
    use HasFactory;

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
    protected $table = 'nom_orchestrators';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'pillar_id',
        'account_id',
        'is_active',
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
        ];
    }

    public static function getOnlinePercent(): float
    {
        $total = self::count();
        $online = self::whereActive()->count();
        $percent = ($online / $total) * 100;

        return round($percent, 1);
    }

    public static function getRequiredOnlinePercent(): float
    {
        $total = self::count();
        $required = ceil($total * 0.66) + 1;
        $percent = ($required / $total) * 100;

        return round($percent, 1);
    }

    //
    // Relations

    public function pillar(): BelongsTo
    {
        return $this->belongsTo(Pillar::class);
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    //
    // Scopes

    public function scopeWhereActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeWhereInactive($query)
    {
        return $query->where('is_active', false);
    }
}
