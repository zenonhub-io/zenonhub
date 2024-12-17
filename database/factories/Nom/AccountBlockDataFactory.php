<?php

declare(strict_types=1);

namespace Database\Factories\Nom;

use App\Models\Nom\AccountBlock;
use App\Models\Nom\AccountBlockData;
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
