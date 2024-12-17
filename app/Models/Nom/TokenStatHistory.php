<?php

declare(strict_types=1);

namespace App\Models\Nom;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TokenStatHistory extends Model
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
    protected $table = 'nom_token_stat_histories';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'token_id',
        'daily_minted',
        'daily_burned',
        'total_supply',
        'total_holders',
        'total_transactions',
        'total_transferred',
        'total_locked',
        'total_wrapped',
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
            'daily_minted' => 'string',
            'daily_burned' => 'string',
            'total_supply' => 'string',
            'date' => 'date',
        ];
    }

    //
    // Relations

    public function token(): BelongsTo
    {
        return $this->belongsTo(Token::class);
    }
}
