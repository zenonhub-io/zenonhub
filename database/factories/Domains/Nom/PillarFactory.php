<?php

declare(strict_types=1);

namespace Database\Factories\Domains\Nom;

use App\Domains\Nom\Models\Account;
use App\Domains\Nom\Models\Pillar;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;

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
            'slug' => fake()->slug(),
            'qsr_burn' => Pillar::max('qsr_burn') + (10000 * NOM_DECIMALS),
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
                'qsr_burn' => Pillar::max('qsr_burn') + (150000 * NOM_DECIMALS),
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
