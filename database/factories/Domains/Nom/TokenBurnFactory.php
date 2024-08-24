<?php

declare(strict_types=1);

namespace Database\Factories\Domains\Nom;

use App\Domains\Nom\Models\Account;
use App\Domains\Nom\Models\AccountBlock;
use App\Domains\Nom\Models\Token;
use App\Domains\Nom\Models\TokenBurn;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;

class TokenBurnFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<Model>
     */
    protected $model = TokenBurn::class;

    public function definition(): array
    {
        return [
            'chain_id' => 1,
            'token_id' => Token::factory()->create(),
            'account_id' => Account::factory()->create(),
            'account_block_id' => AccountBlock::factory()->create(),
            'amount' => 100 * NOM_DECIMALS,
            'created_at' => now(),
        ];
    }
}
