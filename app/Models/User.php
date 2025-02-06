<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Nom\Account;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasName;
use Filament\Panel;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements FilamentUser, HasName, MustVerifyEmail
{
    use HasApiTokens, HasFactory, HasRoles, Notifiable, TwoFactorAuthenticatable;

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
        'registration_ip',
        'last_seen_at',
        'email_verified_at',
        'last_login_at',
        'created_at',
        'updated_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'registration_ip',
        'login_ip',
        'two_factor_recovery_codes',
        'two_factor_secret',
        'email_verified_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'email_verified_at' => 'datetime',
            'two_factor_confirmed_at' => 'datetime',
            'last_login_at' => 'datetime',
            'last_seen_at' => 'datetime',
        ];
    }

    //
    // Relations

    public function verifiedAccounts(): BelongsToMany
    {
        return $this->belongsToMany(
            Account::class,
            'user_nom_verified_accounts_pivot',
            'user_id',
            'account_id'
        )->withPivot('nickname', 'verified_at');
    }

    public function favoriteAccounts(): BelongsToMany
    {
        return $this->belongsToMany(
            Account::class,
            'markable_favorites',
            'user_id',
            'markable_id'
        )->wherePivot('markable_type', Account::class)
            ->withPivot('label', 'metadata', 'created_at', 'updated_at');
    }

    public function notificationTypes(): BelongsToMany
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

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->hasRole('admin');
    }

    public function getFilamentName(): string
    {
        return $this->username;
    }
}
