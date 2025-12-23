<?php

declare(strict_types=1);

namespace Database\Seeders\Site;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::create([
            'name' => 'admin',
            'username' => 'admin',
            'email' => 'admin@local.test',
            'password' => Hash::make('devpass'),
            'registration_ip' => '127.0.0.1',
            'email_verified_at' => now(),
        ]);

        $user->roles()->sync([1]);
    }
}
