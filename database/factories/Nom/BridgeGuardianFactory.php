<?php

declare(strict_types=1);

namespace Database\Factories\Nom;

use App\Models\Nom\Account;
use App\Models\Nom\BridgeGuardian;
use Illuminate\Database\Eloquent\Factories\Factory;

class BridgeGuardianFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<Model>
     */
    protected $model = BridgeGuardian::class;

    public function definition(): array
    {
        return [
            'account_id' => Account::factory(),
            'nominated_at' => now(),
        ];
    }

    public function accepted(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'accepted_at' => now(),
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
