<?php

namespace App\Models;

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
        'category',
        'description',
        'data',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'data' => 'array',
    ];

    //
    // Scopes

    public function scopeIsActive($query)
    {
        return $query->where('is_active', '1');
    }

    //
    // Attributes

    public function getSubscribedUsersAttribute()
    {
        return User::whereHas('notification_types', function ($query) {
            return $query->where('code', $this->code);
        })->get();
    }

    //
    // Methods

    public static function findByCode(string $code): ?NotificationType
    {
        return static::where('code', $code)->first();
    }

    public static function getSubscribedUsers(string $code)
    {
        return self::findByCode($code)->subscribed_users;
    }

    public static function getAllCategories()
    {
        return self::select('category')->groupBy('category')->pluck('category')->sortBy(function ($item, $key) {
            if ($item === 'network') {
                return -1;
            }

            return $key;
        });
    }
}
