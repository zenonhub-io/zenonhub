<?php

declare(strict_types=1);

namespace Database\Factories\Domains\Nom;

use App\Domains\Nom\Enums\AccountBlockTypesEnum;
use App\Domains\Nom\Models\Account;
use App\Domains\Nom\Models\AccountBlock;
use App\Domains\Nom\Models\Momentum;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;

class AccountBlockFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<Model>
     */
    protected $model = AccountBlock::class;

    public function definition(): array
    {
        return [
            'chain_id' => 1,
            'account_id' => Account::factory(),
            'to_account_id' => Account::factory(),
            'momentum_id' => Momentum::factory(),
            'momentum_acknowledged_id' => function (array $attributes) {
                $momentum = Momentum::find($attributes['momentum_id']);

                return $momentum->previous_momentum?->id ?: $momentum->id;
            },
            'parent_id' => null,
            'token_id' => null,
            'contract_method_id' => null,
            'version' => 1,
            'block_type' => AccountBlockTypesEnum::SEND->value,
            'height' => function (array $attributes) {
                $accountChainHeight = AccountBlock::whereRelation('account', 'id', $attributes['account_id'])->max('height');

                return ($accountChainHeight ?: 0) + 1;
            },
            'amount' => '0',
            'hash' => hash('sha256', random_bytes(16)),
            'created_at' => now(),
        ];
    }
}
