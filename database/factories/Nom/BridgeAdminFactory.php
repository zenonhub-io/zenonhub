<?php

declare(strict_types=1);

namespace Database\Factories\Nom;

use App\Models\Nom\Account;
use App\Models\Nom\BridgeAdmin;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;

class BridgeAdminFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<Model>
     */
    protected $model = BridgeAdmin::class;

    public function definition(): array
    {
        return [
            'account_id' => Account::factory(),
            'nominated_by_id' => Account::factory(),
            'nominated_at' => now(),
        ];
    }

    public function accepted(): Factory
    {
        return $this->state(fn (array $attributes) => [
            'accepted_at' => now(),
        ]);
    }

    public function revoked(): Factory
    {
        return $this->state(fn (array $attributes) => [
            'revoked_at' => now(),
        ]);
    }
}
