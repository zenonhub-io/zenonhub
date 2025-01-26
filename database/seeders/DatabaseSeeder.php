<?php

declare(strict_types=1);

namespace Database\Seeders;

use Database\Seeders\Site\NotificationTypesSeeder;
use Database\Seeders\Site\UserRolesSeeder;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            NotificationTypesSeeder::class,
            UserRolesSeeder::class,
        ]);
    }
}
