<?php

declare(strict_types=1);

namespace Database\Factories\Domains\Nom;

use App\Domains\Nom\Models\Account;
use App\Domains\Nom\Models\Sentinel;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;

class SentinelFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<Model>
     */
    protected $model = Sentinel::class;

    public function definition(): array
    {
        return [
            'chain_id' => 1,
            'owner_id' => Account::factory(),
            'created_at' => now(),
        ];
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
