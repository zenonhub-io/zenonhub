<?php

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
        Role::create(['name' => 'member']);
        Role::create(['name' => 'user']);

        Role::create(['name' => 'full', 'guard_name' => 'api']);
        Role::create(['name' => 'basic', 'guard_name' => 'api']);
    }
}
