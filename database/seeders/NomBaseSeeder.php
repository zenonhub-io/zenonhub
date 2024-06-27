<?php

declare(strict_types=1);

namespace Database\Seeders;

use Database\Seeders\Nom\AccountsSeeder;
use Database\Seeders\Nom\BridgeSeeder;
use Database\Seeders\Nom\ChainsSeeder;
use Database\Seeders\Nom\ContractMethodSeeder;
use Database\Seeders\Nom\TokensSeeder;
use Illuminate\Database\Seeder;

class NomBaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            ChainsSeeder::class,
            AccountsSeeder::class,
            ContractMethodSeeder::class,
            BridgeSeeder::class,
            TokensSeeder::class,
        ]);
    }
}
