<?php

namespace App\Models\Nom;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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


    /*
     * Relations
     */

    public function contract()
    {
        return $this->belongsTo(Contract::class, 'contract_id', 'id');
    }


    /*
     * methods
     */

    public static function findByFingerprint(string $fingerprint): ?ContractMethod
    {
        return static::where('fingerprint', $fingerprint)->first();
    }
}
