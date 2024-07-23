<?php

declare(strict_types=1);

namespace App\Domains\Nom\Models;

use Illuminate\Database\Eloquent\Model;

class PublicNode extends Model
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
    protected $table = 'nom_public_nodes';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'ip',
        'version',
        'isp',
        'city',
        'region',
        'country',
        'country_code',
        'latitude',
        'longitude',
        'is_active',
        'discovered_at',
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
            'discovered_at' => 'datetime',
        ];
    }

    //
    // Relations

    //
    // Scopes

    public function scopeWhereActive($query)
    {
        return $query->where('is_active', true);
    }

    //
    // Attributes

    //
    // Methods
}
