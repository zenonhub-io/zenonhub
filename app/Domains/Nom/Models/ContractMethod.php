<?php

declare(strict_types=1);

namespace App\Domains\Nom\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContractMethod extends Model
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
    protected $table = 'nom_contract_methods';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'contract_id',
        'name',
        'signature',
        'fingerprint',
    ];

    //
    // Methods

    public static function findByFingerprint(string $fingerprint): ?ContractMethod
    {
        return static::where('fingerprint', $fingerprint)->first();
    }

    //
    // Relations

    public function contract(): BelongsTo
    {
        return $this->belongsTo(Contract::class);
    }

    //
    // Attributes

    public function getJobClassNameAttribute(): string
    {
        return "App\Jobs\Nom\\{$this->contract->name}\\{$this->name}";
    }
}