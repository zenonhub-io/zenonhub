<?php

declare(strict_types=1);

namespace Database\Factories\Nom;

use App\Models\Nom\BridgeNetwork;
use App\Models\Nom\BridgeNetworkToken;
use App\Models\Nom\Token;
use Illuminate\Database\Eloquent\Factories\Factory;

class BridgeNetworkTokenFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = BridgeNetworkToken::class;

    public function definition(): array
    {
        return [
            'bridge_network_id' => BridgeNetwork::factory(),
            'token_id' => Token::factory(),
            'token_address' => '0x' . bin2hex(random_bytes(20)),
            'min_amount' => fake()->randomNumber(),
            'fee_percentage' => fake()->randomFloat(2, 0, 1),
            'redeem_delay' => fake()->randomNumber(),
            'metadata' => [],
            'is_bridgeable' => fake()->boolean,
            'is_redeemable' => fake()->boolean,
            'is_owned' => fake()->boolean,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
