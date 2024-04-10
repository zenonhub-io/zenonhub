<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\NotificationType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        NotificationType::truncate();
        Role::truncate();
        Schema::enableForeignKeyConstraints();

        $this->call([
            NotificationTypesSeeder::class,
            UserRolesSeeder::class,
        ]);
    }
}
