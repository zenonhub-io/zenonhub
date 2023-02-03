<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotificationType extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'notification_types';

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
        'name',
        'code',
        'type',
        'description',
        'content',
        'link',
        'data',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'data' => 'array'
    ];

    /*
     * Scopes
     */

    public function scopeIsActive($query)
    {
        return $query->where('is_active', '1');
    }

    public function scopeIsGeneral($query)
    {
        return $query->where('type', 'general');
    }

    public function scopeIsDelegator($query)
    {
        return $query->where('type', 'delegator');
    }

    public function scopeIsPillar($query)
    {
        return $query->where('type', 'pillar');
    }


    /*
     * Attributes
     */

    /*
     * Methods
     */

    public static function findByCode(string $code): ?NotificationType
    {
        return static::where('code', $code)->first();
    }
}
