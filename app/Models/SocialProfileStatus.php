<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SocialProfileStatus extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'social_profile_statuses';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'status',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'total_likes' => 'integer',
            'total_views' => 'integer',
            'total_comments' => 'integer',
        ];
    }

    //
    // Relations

    public function profile(): BelongsTo
    {
        return $this->belongsTo(SocialProfile::class);
    }

    //
    // Scopes
}
