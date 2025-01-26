<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Advert;
use App\Models\Nom\AccountBlock;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class AdvertFactory extends Factory
{
    /**
     * {@inheritdoc}
     */
    protected $model = Advert::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'account_block_id' => AccountBlock::factory(),
            'title' => $this->faker->words(3, true),
            'headline' => $this->faker->words(5, true),
            'body' => $this->faker->words(10, true),
            'cta_text' => $this->faker->words(2, true),
            'cta_link' => $this->faker->url,
            'image' => $this->faker->imageUrl(640, 480, 'business', true, 'Logo'),
            'owner_name' => $this->faker->name . ' ' . $this->faker->lastName,
            'owner_contact' => $this->faker->safeEmail,
            'placement' => 'sidebar',
            'display_order' => 0,
            'display_count' => 0,
            'starts_at' => $this->faker->optional()->dateTime,
            'ends_at' => $this->faker->optional()->dateTime,
            'is_confirmed' => $this->faker->boolean,
        ];
    }

    public function confirmed(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_confirmed' => true,
        ]);
    }

    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'starts_at' => now()->subDays(1),
            'ends_at' => now()->addDays(1),
        ]);
    }

    public function permanent(): static
    {
        return $this->state(fn (array $attributes) => [
            'starts_at' => now()->subDays(1),
            'ends_at' => null,
        ]);
    }
}
