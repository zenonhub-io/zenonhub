<?php

declare(strict_types=1);

namespace App\Domains\Nom\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContractMethod extends Model
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

    public static function findByContractMethod(string $contract, string $method): ?ContractMethod
    {
        return self::whereRelation('contract', 'name', $contract)
            ->firstWhere('name', $method);
    }

    //
    // Relations

    public function contract(): BelongsTo
    {
        return $this->belongsTo(Contract::class);
    }

    //
    // Attributes

    //
    // Methods
}
