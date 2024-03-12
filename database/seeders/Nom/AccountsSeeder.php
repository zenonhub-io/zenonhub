<?php

declare(strict_types=1);

namespace Database\Seeders\Nom;

use App\Classes\Utilities;
use App\Models\Nom\Account;
use Illuminate\Database\Seeder;

class AccountsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Empty address
        Account::insert([
            'chain_id' => Utilities::loadChain()->id,
            'address' => Account::ADDRESS_EMPTY,
            'name' => 'Empty address',
            'is_embedded_contract' => false,
        ]);

        // Embedded contracts
        foreach (Account::EMBEDDED_CONTRACTS as $address => $name) {
            Account::insert([
                'chain_id' => Utilities::loadChain()->id,
                'address' => $address,
                'name' => $name,
                'is_embedded_contract' => true,
            ]);
        }

        // Named addresses
        $namedAccounts = config('explorer.named_accounts');
        foreach ($namedAccounts as $address => $name) {
            Account::insert([
                'chain_id' => Utilities::loadChain()->id,
                'address' => $address,
                'name' => $name,
                'is_embedded_contract' => false,
            ]);
        }
    }
}
