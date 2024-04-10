<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Domains\Nom\Models\Account;
use App\Domains\Nom\Models\AccountBlock;
use App\Domains\Nom\Models\AccountBlockData;
use App\Domains\Nom\Models\Chain;
use App\Domains\Nom\Models\Contract;
use App\Domains\Nom\Models\ContractMethod;
use App\Domains\Nom\Models\Momentum;
use App\Domains\Nom\Models\Pillar;
use App\Domains\Nom\Models\Token;
use Database\Seeders\Nom\AccountsSeeder;
use Database\Seeders\Nom\ChainsSeeder;
use Database\Seeders\Nom\ContractMethodSeeder;
use Database\Seeders\Nom\Test\AccountBlocksSeeder;
use Database\Seeders\Nom\Test\MomentumsSeeder;
use Database\Seeders\Nom\Test\PillarsSeeder;
use Database\Seeders\Nom\TokensSeeder;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class TestGenesisSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        Chain::truncate();
        Account::truncate();
        Contract::truncate();
        ContractMethod::truncate();
        Token::truncate();
        Pillar::truncate();
        Momentum::truncate();
        AccountBlock::truncate();
        AccountBlockData::truncate();
        Schema::enableForeignKeyConstraints();

        $this->call([
            ChainsSeeder::class,
            AccountsSeeder::class,
            ContractMethodSeeder::class,
            TokensSeeder::class,
            PillarsSeeder::class,
            MomentumsSeeder::class,
            AccountBlocksSeeder::class,
        ]);
    }
}
