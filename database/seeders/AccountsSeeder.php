<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class AccountsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach (\App\Models\Nom\Account::EMBEDDED_CONTRACTS as $address => $name) {
            \App\Models\Nom\Account::insert([
                'address' => $address,
                'name' => $name,
                'is_embedded_contract' => true,
            ]);
        }
    }
}
