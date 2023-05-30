<?php

namespace App\Models;

use App\Models\Nom\Account;
use Hash;
use Illuminate\Auth\Passwords\CanResetPassword as CanResetPasswordTrait;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements MustVerifyEmail
{
    use CanResetPasswordTrait, HasApiTokens, HasFactory, HasRoles, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'privacy_confirmed_at',
        'last_login_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'privacy_confirmed_at' => 'datetime',
        'last_login_at' => 'datetime',
    ];

    //
    // Relations

    public function nom_accounts()
    {
        return $this->belongsToMany(
            Account::class,
            'user_nom_accounts_pivot',
            'user_id',
            'account_id'
        )->withPivot('nickname', 'is_pillar', 'is_sentinel', 'is_default', 'verified_at');
    }

    public function notification_types(): BelongsToMany
    {
        return $this->belongsToMany(
            NotificationType::class,
            'notification_subscriptions',
            'user_id',
            'type_id'
        )->using(NotificationSubscription::class)->withPivot('data', 'created_at', 'updated_at');
    }

    //
    // Attributes

    public function getIsPillarOwnerAttribute()
    {
        return $this->nom_accounts()->wherePivot('is_pillar', '1')->exists();
    }

    public function getIsSentinelOwnerAttribute()
    {
        return $this->nom_accounts()->wherePivot('is_sentinel', '1')->exists();
    }

    public function setPasswordAttribute($value): void
    {
        $this->attributes['password'] = Hash::make($value);
    }
}
