<?php

declare(strict_types=1);

namespace Database\Factories\Nom;

use App\Enums\Nom\AccountRewardTypesEnum;
use App\Enums\Nom\NetworkTokensEnum;
use App\Models\Nom\Account;
use App\Models\Nom\AccountBlock;
use App\Models\Nom\AccountReward;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;

class AccountRewardFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<Model>
     */
    protected $model = AccountReward::class;

    public function definition(): array
    {
        return [
            'chain_id' => 1,
            'account_block_id' => AccountBlock::factory(),
            'account_id' => Account::factory(),
            'token_id' => load_token(NetworkTokensEnum::ZNN->zts())->id,
            'type' => AccountRewardTypesEnum::DELEGATE,
            'amount' => 50 * config('nom.decimals'),
            'created_at' => now(),
        ];
    }

    public function pillar(): Factory
    {
        return $this->state(fn (array $attributes) => [
            'status' => AccountRewardTypesEnum::PILLAR,
        ]);
    }

    public function stake(): Factory
    {
        return $this->state(fn (array $attributes) => [
            'token_id' => load_token(NetworkTokensEnum::QSR->zts())->id,
            'status' => AccountRewardTypesEnum::STAKE,
        ]);
    }

    public function sentinel(): Factory
    {
        return $this->state(fn (array $attributes) => [
            'status' => AccountRewardTypesEnum::SENTINEL,
        ]);
    }

    public function liquidity(): Factory
    {
        return $this->state(fn (array $attributes) => [
            'status' => AccountRewardTypesEnum::SENTINEL,
        ]);
    }

    public function bridgeAffiliate(): Factory
    {
        return $this->state(fn (array $attributes) => [
            'status' => AccountRewardTypesEnum::BRIDGE_AFFILIATE,
        ]);
    }
}
