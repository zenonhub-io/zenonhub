<?php

declare(strict_types=1);

namespace Database\Factories\Nom;

use App\Enums\Nom\AcceleratorProjectStatusEnum;
use App\Models\Nom\AcceleratorProject;
use App\Models\Nom\Account;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class AcceleratorProjectFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<Model>
     */
    protected $model = AcceleratorProject::class;

    public function definition(): array
    {
        return [
            'chain_id' => 1,
            'owner_id' => Account::factory(),
            'hash' => fake()->sha256(),
            'name' => fake()->title(),
            'slug' => fn (array $attributes) => Str::slug($attributes['name']),
            'url' => fake()->url(),
            'description' => fake()->paragraph(),
            'status' => AcceleratorProjectStatusEnum::NEW,
            'znn_requested' => (string) (5000 * NOM_DECIMALS),
            'qsr_requested' => (string) (50000 * NOM_DECIMALS),
            'created_at' => now(),
        ];
    }

    public function accepted(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => AcceleratorProjectStatusEnum::ACCEPTED,
            ];
        });
    }

    public function rejected(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => AcceleratorProjectStatusEnum::REJECTED,
            ];
        });
    }

    public function complete(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'znn_remaining' => 0,
                'qsr_remaining' => 0,
                'znn_paid' => $attributes['znn_requested'],
                'qsr_paid' => $attributes['qsr_requested'],
                'status' => AcceleratorProjectStatusEnum::COMPLETE,
            ];
        });
    }
}
