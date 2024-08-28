<?php

declare(strict_types=1);

namespace Database\Factories\Domains\Nom;

use App\Domains\Nom\Enums\EmbeddedContractsEnum;
use App\Domains\Nom\Enums\NetworkTokensEnum;
use App\Domains\Nom\Models\Account;
use App\Domains\Nom\Models\Token;
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
        return $this->state(function (array $attributes) {
            return [
                'owner_id' => load_account(EmbeddedContractsEnum::TOKEN->value),
                'name' => 'ZNN',
                'symbol' => 'ZNN',
                'domain' => 'zenon.network',
                'token_standard' => NetworkTokensEnum::ZNN->value,
            ];
        });
    }

    public function qsr(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'owner_id' => load_account(EmbeddedContractsEnum::TOKEN->value),
                'name' => 'QSR',
                'symbol' => 'QSR',
                'domain' => 'zenon.network',
                'token_standard' => NetworkTokensEnum::QSR->value,
            ];
        });
    }
}
