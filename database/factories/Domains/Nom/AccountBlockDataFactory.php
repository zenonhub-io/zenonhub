<?php

declare(strict_types=1);

namespace Database\Factories\Domains\Nom;

use App\Domains\Nom\Models\AccountBlock;
use App\Domains\Nom\Models\AccountBlockData;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;

class AccountBlockDataFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<Model>
     */
    protected $model = AccountBlockData::class;

    public function definition(): array
    {
        return [
            'account_block_id' => AccountBlock::factory(),
            'raw' => '',
            'decoded' => '',
            'is_processed' => 0,
        ];
    }
}
