<?php

declare(strict_types=1);

namespace App\Models\Nom;

use Illuminate\Database\Eloquent\Model;

class NetworkStatHistory extends Model
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
    protected $table = 'nom_network_stat_histories';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'total_tx',
        'total_daily_tx',
        'total_addresses',
        'total_daily_addresses',
        'total_active_addresses',
        'total_tokens',
        'total_daily_tokens',
        'total_stakes',
        'total_daily_stakes',
        'total_staked',
        'total_daily_staked',
        'total_fusions',
        'total_daily_fusions',
        'total_fused',
        'total_daily_fused',
        'total_pillars',
        'total_sentinels',
        'date',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'total_staked' => 'string',
            'total_daily_staked' => 'string',
            'total_fused' => 'string',
            'total_daily_fused' => 'string',
            'date' => 'date',
        ];
    }

    //
    // Relations

}
