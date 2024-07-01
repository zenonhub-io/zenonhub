<?php

declare(strict_types=1);

namespace Database\Seeders;

use Database\Seeders\Nom\Test\AccountBlocksSeeder;
use Database\Seeders\Nom\Test\MomentumsSeeder;
use Database\Seeders\Nom\Test\PillarsSeeder;
use Illuminate\Database\Seeder;

class TestGenesisSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            PillarsSeeder::class,
            MomentumsSeeder::class,
            AccountBlocksSeeder::class,
        ]);
    }
}
