<?php

namespace App\Models\Nom;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Orchestrator extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'nom_orchestrators';

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
    protected $fillable = [
        'pillar_id',
        'account_id',
        'status',
    ];

    public static function getOnlinePercent(): float
    {
        $total = self::count();
        $online = self::isActive()->count();
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
        return $this->belongsTo(Pillar::class, 'pillar_id', 'id');
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'account_id', 'id');
    }

    //
    // Scopes

    public function scopeIsActive($query)
    {
        return $query->where('status', true);
    }

    public function scopeIsInactive($query)
    {
        return $query->where('status', false);
    }
}
