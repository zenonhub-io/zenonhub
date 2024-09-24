<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\SocialProfile;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;

class SocialProfileFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<Model>
     */
    protected $model = SocialProfile::class;

    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'bio' => fake()->paragraph(),
            'avatar' => fake()->imageUrl(),
            'website' => fake()->url(),
            'email' => fake()->unique()->safeEmail(),
            'x' => fake()->url(),
            'medium' => fake()->url(),
            'telegram' => fake()->url(),
            'discord' => fake()->url(),
            'github' => fake()->url(),
        ];
    }
}
