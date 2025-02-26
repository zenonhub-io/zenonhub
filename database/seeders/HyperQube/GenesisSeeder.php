<?php

declare(strict_types=1);

namespace Database\Seeders\HyperQube;

use Database\Seeders\HyperQube\Genesis\AccountBlocksSeeder;
use Database\Seeders\HyperQube\Genesis\AccountsSeeder;
use Database\Seeders\HyperQube\Genesis\MomentumsSeeder;
use Database\Seeders\HyperQube\Genesis\PillarsSeeder;
use Illuminate\Database\Seeder;

class GenesisSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            AccountsSeeder::class,
            PillarsSeeder::class,
            MomentumsSeeder::class,
            // AccountBlocksSeeder::class,
        ]);
    }
}
