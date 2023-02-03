<?php

namespace App\Models\Nom;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccountBlockData extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'nom_account_block_data';

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
        'account_block_id',
        'contract_method_id',
        'raw',
        'decoded',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'decoded' => 'array',
    ];


    /*
     * Relations
     */

    public function account_block()
    {
        return $this->belongsTo(AccountBlock::class, 'account_block_id', 'id');
    }

    public function contract_method()
    {
        return $this->hasOne(ContractMethod::class, 'id', 'contract_method_id');
    }


    /*
     * Attributes
     */

    public function getJsonAttribute()
    {
        return json_encode($this->decoded, JSON_PRETTY_PRINT);
    }
}
