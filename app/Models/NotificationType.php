<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class NotificationType extends Model
{
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
    protected $fillable = [
        'name',
        'code',
        'category',
        'description',
        'data',
        'is_configurable',
        'is_active',
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'notification_types';

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'data' => 'array',
    ];

    //
    // Methods

    public static function getSubscribedUsers(string $code)
    {
        return self::firstWhere('code', $code)->subscribed_users;
    }

    //
    // Scopes

    public function scopeWhereActive($query)
    {
        return $query->where('is_active', '1');
    }

    //
    // Attributes

    public function getSubscribedUsersAttribute(): Collection
    {
        return User::whereRelation('notification_types', 'code', $this->code)->get();
    }

    public function checkUserSubscribed(User $user): bool
    {
        return $user->notificationTypes->contains('id', $this->id);
    }
}
