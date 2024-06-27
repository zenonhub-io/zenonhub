<?php

declare(strict_types=1);

namespace Database\Seeders;

use Database\Seeders\Nom\Genesis\AccountBlocksSeeder;
use Database\Seeders\Nom\Genesis\MomentumsSeeder;
use Database\Seeders\Nom\Genesis\PillarsSeeder;
use Illuminate\Database\Seeder;

class GenesisSeeder extends Seeder
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
