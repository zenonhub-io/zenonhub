<?php

namespace App\Models\Nom;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sentinel extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'nom_sentinels';

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
        'owner_id',
        'is_revocable',
        'revoke_cooldown',
        'active',
        'created_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'created_at' => 'datetime',
    ];


    /*
     * Relations
     */

    public function owner()
    {
        return $this->belongsTo(Account::class, 'owner_id', 'id');
    }


    /*
     * Scopes
     */

    public function scopeIsActive($query)
    {
        return $query->where('is_active', '1')->whereNull('revoked_at');
    }
}
