<?php

declare(strict_types=1);

namespace App\Models\Nom;

use Illuminate\Database\Eloquent\Model;

class Currency extends Model
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
    protected $table = 'nom_currencies';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'name',
        'symbol',
        'icon',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [];
    }

    //
    // Attributes

    //
    // Relations

    //
    // Scopes

    //
    // Methods
}
