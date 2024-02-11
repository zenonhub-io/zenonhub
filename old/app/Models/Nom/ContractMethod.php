<?php

namespace App\Models\Nom;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContractMethod extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'nom_contract_methods';

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
        'contract_id',
        'name',
        'signature',
        'fingerprint',
    ];

    //
    // Relations

    public function contract(): BelongsTo
    {
        return $this->belongsTo(Contract::class, 'contract_id', 'id');
    }

    //
    // Attributes

    public function getJobClassNameAttribute()
    {
        return "App\Jobs\Nom\\{$this->contract->name}\\{$this->name}";
    }

    //
    // Methods

    public static function findByFingerprint(string $fingerprint): ?ContractMethod
    {
        return static::where('fingerprint', $fingerprint)->first();
    }
}
