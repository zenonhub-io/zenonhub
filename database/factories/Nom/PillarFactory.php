<?php

declare(strict_types=1);

namespace Database\Factories\Nom;

use App\Models\Nom\Account;
use App\Models\Nom\Pillar;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class PillarFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<Model>
     */
    protected $model = Pillar::class;

    public function definition(): array
    {
        return [
            'chain_id' => 1,
            'owner_id' => Account::factory(),
            'producer_account_id' => Account::factory(),
            'withdraw_account_id' => Account::factory(),
            'rank' => Pillar::max('rank') + 1,
            'name' => fake()->word(),
            'slug' => fn (array $attributes) => Str::slug($attributes['name']),
            'qsr_burn' => Pillar::max('qsr_burn') + (10000 * config('nom.decimals')),
            'momentum_rewards' => 0,
            'delegate_rewards' => 0,
            'is_legacy' => 0,
            'created_at' => now(),
        ];
    }

    public function legacy(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'qsr_burn' => 150000 * config('nom.decimals'),
                'is_legacy' => 1,
            ];
        });
    }

    public function revoked(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'revoked_at' => now(),
            ];
        });
    }
}
