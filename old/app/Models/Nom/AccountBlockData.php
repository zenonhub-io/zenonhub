<?php

namespace App\Models\Nom;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
        'raw',
        'decoded',
        'is_processed',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'decoded' => 'array',
    ];

    //
    // Relations

    public function account_block(): BelongsTo
    {
        return $this->belongsTo(AccountBlock::class, 'account_block_id', 'id');
    }

    //
    // Attributes

    public function getJsonAttribute()
    {
        return json_encode($this->decoded, JSON_PRETTY_PRINT);
    }

    public function getParsedAttribute()
    {
        return base64_decode($this->raw);
    }
}
