<?php

declare(strict_types=1);

namespace Database\Seeders\Nom;

use Database\Seeders\Nom\Network\AccountsSeeder;
use Database\Seeders\Nom\Network\BridgeSeeder;
use Database\Seeders\Nom\Network\ContractMethodSeeder;
use Database\Seeders\Nom\Network\TokensSeeder;
use Illuminate\Database\Seeder;

class NetworkSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            AccountsSeeder::class,
            ContractMethodSeeder::class,
            BridgeSeeder::class,
            TokensSeeder::class,
        ]);
    }
}
