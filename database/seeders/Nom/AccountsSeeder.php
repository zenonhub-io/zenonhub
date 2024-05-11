<?php

declare(strict_types=1);

namespace Database\Seeders\Nom;

use App\Domains\Nom\Enums\EmbeddedContractsEnum;
use App\Domains\Nom\Models\Account;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class AccountsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $chainId = app('currentChain')->id;

        Account::insert([
            'chain_id' => $chainId,
            'address' => config('explorer.empty_address'),
            'name' => 'Empty address',
            'is_embedded_contract' => false,
        ]);

        foreach (EmbeddedContractsEnum::cases() as $address) {
            Account::insert([
                'chain_id' => $chainId,
                'address' => $address->value,
                'name' => $address->label(),
                'is_embedded_contract' => true,
            ]);
        }

        $accounts = Storage::json('nom-json/genesis/accounts.json');
        foreach ($accounts as $account) {
            Account::updateOrInsert([
                'chain_id' => $chainId,
                'address' => $account['address'],
            ], [
                'genesis_znn_balance' => $account['znn_balance'],
                'genesis_qsr_balance' => $account['qsr_balance'],
            ]);
        }
    }
}
