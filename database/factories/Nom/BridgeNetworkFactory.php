<?php

declare(strict_types=1);

namespace Database\Factories\Nom;

use App\Models\Nom\BridgeNetwork;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;

class BridgeNetworkFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<Model>
     */
    protected $model = BridgeNetwork::class;

    public function definition(): array
    {
        return [
            'chain_id' => '1',
            'chain_identifier' => '1',
            'network_class' => fake()->randomNumber(4),
            'name' => fake()->word(),
            'contract_address' => '0x' . bin2hex(random_bytes(20)),
            'meta_data' => [],
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
