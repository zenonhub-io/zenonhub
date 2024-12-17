<?php

declare(strict_types=1);

namespace Database\Factories\Nom;

use App\Models\Nom\Account;
use App\Models\Nom\AccountBlock;
use App\Models\Nom\Token;
use App\Models\Nom\TokenMint;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;

class TokenMintFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<Model>
     */
    protected $model = TokenMint::class;

    public function definition(): array
    {
        return [
            'chain_id' => 1,
            'token_id' => Token::factory()->create(),
            'issuer_id' => Account::factory()->create(),
            'receiver_id' => Account::factory()->create(),
            'account_block_id' => AccountBlock::factory()->create(),
            'amount' => 100 * NOM_DECIMALS,
            'created_at' => now(),
        ];
    }
}
