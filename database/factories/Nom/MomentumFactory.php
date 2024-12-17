<?php

declare(strict_types=1);

namespace Database\Factories\Nom;

use App\Models\Nom\Momentum;
use App\Models\Nom\Pillar;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;

class MomentumFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<Model>
     */
    protected $model = Momentum::class;

    public function definition(): array
    {
        return [
            'chain_id' => 1,
            'producer_pillar_id' => Pillar::inRandomOrder()->first()->id,
            'producer_account_id' => fn (array $attributes) => Pillar::find($attributes['producer_pillar_id'])->producer_account_id,
            'version' => 1,
            'height' => Momentum::max('height') + 1,
            'hash' => fake()->sha256(),
            'created_at' => now(),
        ];
    }
}
