<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Nom\TimeChallenge;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;

class TimeChallengeFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<Model>
     */
    protected $model = TimeChallenge::class;

    public function definition(): array
    {
        return [
            'chain_id' => 1,
            'hash' => hash('sha256', random_bytes(16)),
            'delay' => 5,
            'start_height' => 1,
            'end_height' => 6,
            'is_active' => true,
            'created_at' => now(),
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}
