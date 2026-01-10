<?php

declare(strict_types=1);

namespace Database\Factories\Nom;

use App\Models\Nom\Account;
use App\Models\Nom\AccountBlock;
use App\Models\Nom\Plasma;
use App\View\Components\DateTime\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;

class PlasmaFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<Model>
     */
    protected $model = Plasma::class;

    public function definition(): array
    {
        return [
            'chain_id' => 1,
            'from_account_id' => Account::factory(),
            'to_account_id' => Account::factory(),
            'account_block_id' => AccountBlock::factory(),
            'amount' => (string) (1 * config('nom.decimals')),
            'started_at' => fn (array $attributes) => AccountBlock::find($attributes['account_block_id'])->created_at,
            'ended_at' => null,
        ];
    }

    public function ended(?Carbon $endDate = null): Factory
    {
        return $this->state(fn (array $attributes) => [
            'ended_at' => $endDate ?: now(),
        ]);
    }
}
