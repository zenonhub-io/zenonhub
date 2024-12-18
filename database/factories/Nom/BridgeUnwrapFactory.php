<?php

declare(strict_types=1);

namespace Database\Factories\Nom;

use App\Models\Nom\Account;
use App\Models\Nom\AccountBlock;
use App\Models\Nom\BridgeNetwork;
use App\Models\Nom\BridgeUnwrap;
use App\Models\Nom\Token;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;

class BridgeUnwrapFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<Model>
     */
    protected $model = BridgeUnwrap::class;

    public function definition(): array
    {
        return [
            'bridge_network_id' => BridgeNetwork::factory(),
            'to_account_id' => Account::factory(),
            'token_id' => Token::factory(),
            'account_block_id' => AccountBlock::factory(),
            'from_address' => '0x' . bin2hex(random_bytes(20)),
            'transaction_hash' => '0x' . bin2hex(random_bytes(32)),
            'log_index' => 1,
            'amount' => fake()->randomDigit() * config('nom.decimals'),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    public function withSignature(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'signature' => base64_decode(bin2hex(random_bytes(10))),
            ];
        });
    }

    public function affiliate(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'log_index' => 4000000001,
            ];
        });
    }

    public function redeemed(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'redeemed_at' => now(),
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
