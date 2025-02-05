<?php

declare(strict_types=1);

namespace Database\Seeders\Site;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Role;

class UserRolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        Role::truncate();
        Schema::enableForeignKeyConstraints();

        Role::create(['name' => 'admin']);
        Role::create(['name' => 'vip']);
        Role::create(['name' => 'premium']);
        Role::create(['name' => 'free']);
    }
}
