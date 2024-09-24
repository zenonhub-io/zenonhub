<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\SocialProfileFactory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class SocialProfile extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'social_profiles';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'name',
        'bio',
        'avatar',
        'website',
        'email',
        'x',
        'telegram',
        'github',
        'medium',
        'discord',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<string>
     */
    protected $hidden = [
        'id',
        'profileable_type',
        'profileable_id',
        'created_at',
        'updated_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
        ];
    }

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory(): Factory
    {
        return SocialProfileFactory::new();
    }

    public static function findByProfileableType(string $type, int $id): ?SocialProfile
    {
        return self::where('profileable_type', $type)
            ->where('profileable_id', $id)
            ->first();
    }

    //
    // Relations

    public function profileable(): MorphTo
    {
        return $this->morphTo();
    }

    public function statuses(): HasMany
    {
        return $this->hasMany(SocialProfileStatus::class, 'social_profile_id');
    }

    //
    // Scopes
}
