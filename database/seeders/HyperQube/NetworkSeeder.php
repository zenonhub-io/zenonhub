<?php

declare(strict_types=1);

namespace Database\Seeders\HyperQube;

use Database\Seeders\HyperQube\Network\AccountsSeeder;
use Database\Seeders\HyperQube\Network\BridgeSeeder;
use Database\Seeders\HyperQube\Network\ContractMethodSeeder;
use Database\Seeders\HyperQube\Network\TokensSeeder;
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
