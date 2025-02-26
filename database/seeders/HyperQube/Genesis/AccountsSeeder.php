<?php

declare(strict_types=1);

namespace Database\Seeders\HyperQube\Genesis;

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
        $chain = app('currentChain');
        $accounts = Storage::json('json/hqz/genesis.json')['GenesisBlocks']['Blocks'];

        collect($accounts)->each(function ($accountData) use ($chain) {
            Account::updateOrInsert([
                'chain_id' => $chain->id,
                'address' => $accountData['Address'],
            ], [
                'genesis_znn_balance' => $accountData['BalanceList']['zts1utylzxxxxxxxxxxx6agxt0'] ?? 0,
                'genesis_qsr_balance' => $accountData['BalanceList']['zts1utylqxxxxxxxxxxxdzq2gc'] ?? 0,
            ]);
        });
    }
}
