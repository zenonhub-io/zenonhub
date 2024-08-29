<?php

declare(strict_types=1);

namespace Database\Seeders\Nom\Genesis;

use App\Models\Nom\Account;
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
        $accounts = Storage::json('nom-json/genesis/genesis.json')['GenesisBlocks']['Blocks'];

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
