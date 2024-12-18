<?php

declare(strict_types=1);

namespace Database\Factories\Nom;

use App\Enums\Nom\AcceleratorPhaseStatusEnum;
use App\Models\Nom\AcceleratorPhase;
use App\Models\Nom\AcceleratorProject;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class AcceleratorPhaseFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<Model>
     */
    protected $model = AcceleratorPhase::class;

    public function definition(): array
    {
        return [
            'project_id' => AcceleratorProject::factory(),
            'hash' => fake()->sha256(),
            'name' => fake()->title(),
            'slug' => fn (array $attributes) => Str::slug($attributes['name']),
            'url' => fake()->url(),
            'description' => fake()->paragraph(),
            'status' => AcceleratorPhaseStatusEnum::OPEN,
            'phase_number' => fn (array $attributes) => AcceleratorPhase::where('project_id', $attributes['project_id'])->max('phase_number') + 1,
            'znn_requested' => (string) (5000 * config('nom.decimals')),
            'qsr_requested' => (string) (50000 * config('nom.decimals')),
            'created_at' => now(),
        ];
    }

    public function paid(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => AcceleratorPhaseStatusEnum::PAID,
            ];
        });
    }
}
