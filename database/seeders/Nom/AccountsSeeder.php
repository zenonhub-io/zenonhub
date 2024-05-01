<?php

declare(strict_types=1);

namespace Database\Seeders\Nom;

use App\Domains\Nom\Enums\EmbeddedContractsEnum;
use App\Domains\Nom\Models\Account;
use Illuminate\Database\Seeder;

class AccountsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Account::insert([
            'chain_id' => app('currentChain')->id,
            'address' => config('explorer.empty_address'),
            'name' => 'Empty address',
            'is_embedded_contract' => false,
        ]);

        foreach (EmbeddedContractsEnum::cases() as $address) {
            Account::insert([
                'chain_id' => app('currentChain')->id,
                'address' => $address->value,
                'name' => $address->label(),
                'is_embedded_contract' => true,
            ]);
        }
    }
}
