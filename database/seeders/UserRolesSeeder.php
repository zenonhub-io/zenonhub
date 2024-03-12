<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class UserRolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Role::create(['name' => 'admin']);
        Role::create(['name' => 'developer']);
        Role::create(['name' => 'member']);
        Role::create(['name' => 'user']);

        Role::create(['name' => 'pro', 'guard_name' => 'api']);
        Role::create(['name' => 'basic', 'guard_name' => 'api']);
    }
}
