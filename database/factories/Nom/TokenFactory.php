<?php

declare(strict_types=1);

namespace Database\Factories\Nom;

use App\Enums\Nom\EmbeddedContractsEnum;
use App\Enums\Nom\NetworkTokensEnum;
use App\Models\Nom\Account;
use App\Models\Nom\Token;
use DigitalSloth\ZnnPhp\Utilities;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;
use Str;

class TokenFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<Model>
     */
    protected $model = Token::class;

    public function definition(): array
    {
        return [
            'chain_id' => 1,
            'owner_id' => Account::factory()->create(),
            'name' => fake()->word(),
            'symbol' => fn (array $attributes) => Str::upper($attributes['name']),
            'domain' => fake()->word() . '.' . fake()->tld(),
            'token_standard' => Utilities::ztsFromHash(fake()->md5()),
            'total_supply' => '0',
            'max_supply' => '9007199254740991',
            'decimals' => 8,
            'is_burnable' => 1,
            'is_mintable' => 1,
            'is_utility' => 1,
            'created_at' => now(),
        ];
    }

    public function znn(): Factory
    {
        return $this->state(fn (array $attributes) => [
            'owner_id' => load_account(EmbeddedContractsEnum::TOKEN->value),
            'name' => NetworkTokensEnum::ZNN->name(),
            'symbol' => NetworkTokensEnum::ZNN->symbol(),
            'domain' => 'zenon.network',
            'token_standard' => NetworkTokensEnum::ZNN->zts(),
        ]);
    }

    public function qsr(): Factory
    {
        return $this->state(fn (array $attributes) => [
            'owner_id' => load_account(EmbeddedContractsEnum::TOKEN->value),
            'name' => NetworkTokensEnum::QSR->name(),
            'symbol' => NetworkTokensEnum::QSR->symbol(),
            'domain' => 'zenon.network',
            'token_standard' => NetworkTokensEnum::QSR->zts(),
        ]);
    }
}
