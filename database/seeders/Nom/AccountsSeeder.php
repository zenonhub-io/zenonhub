<?php

declare(strict_types=1);

namespace Database\Seeders\Nom;

use App\Domains\Nom\Enums\EmbeddedContractsEnum;
use App\Domains\Nom\Models\Account;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class AccountsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        Account::truncate();
        Schema::enableForeignKeyConstraints();

        $chainId = app('currentChain')->id;
        $accounts = Storage::json('nom-json/genesis/genesis.json')['GenesisBlocks']['Blocks'];

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

        collect($accounts)->each(function ($accountData) use ($chainId) {
            Account::updateOrInsert([
                'chain_id' => $chainId,
                'address' => $accountData['Address'],
            ], [
                'genesis_znn_balance' => $accountData['BalanceList']['zts1znnxxxxxxxxxxxxx9z4ulx'] ?? 0,
                'genesis_qsr_balance' => $accountData['BalanceList']['zts1qsrxxxxxxxxxxxxxmrhjll'] ?? 0,
            ]);
        });
    }
}
