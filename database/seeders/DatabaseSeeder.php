<?php

namespace Database\Seeders;

use Database\Seeders\GenesisMomentum\NomAccountBlocksTableSeeder;
use Database\Seeders\GenesisMomentum\NomAccountsTableSeeder;
use Database\Seeders\GenesisMomentum\NomChainsTableSeeder;
use Database\Seeders\GenesisMomentum\NomContractMethodsTableSeeder;
use Database\Seeders\GenesisMomentum\NomContractsTableSeeder;
use Database\Seeders\GenesisMomentum\NomMomentumsTableSeeder;
use Database\Seeders\GenesisMomentum\NomPillarsTableSeeder;
use Database\Seeders\GenesisMomentum\NomTokensTableSeeder;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();

        $this->call([
            NomAccountBlocksTableSeeder::class,
            NomAccountsTableSeeder::class,
            NomChainsTableSeeder::class,
            NomContractMethodsTableSeeder::class,
            NomContractsTableSeeder::class,
            NomMomentumsTableSeeder::class,
            NomPillarsTableSeeder::class,
            NomTokensTableSeeder::class,
            NotificationTypesSeeder::class,
        ]);

        Schema::enableForeignKeyConstraints();
    }
}
