<?php

namespace Database\Seeders;

use Database\Seeders\Nom\AccountsSeeder;
use Database\Seeders\Nom\ChainsSeeder;
use Database\Seeders\Nom\ContractMethodSeeder;
use Database\Seeders\Nom\TokensSeeder;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run() : void
    {
        Schema::disableForeignKeyConstraints();

        $this->call([
            NotificationTypesSeeder::class,
            UserRolesSeeder::class,
            ChainsSeeder::class,
            ContractMethodSeeder::class,
            AccountsSeeder::class,
            TokensSeeder::class,
        ]);

        Schema::enableForeignKeyConstraints();
    }
}
