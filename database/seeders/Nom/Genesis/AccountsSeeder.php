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
        $chain = app('currentChain');
        $accounts = Storage::json('json/nom/genesis.json')['GenesisBlocks']['Blocks'];
        $znnToken = app('znnToken');
        $qsrToken = app('qsrToken');

        collect($accounts)->each(function ($accountData) use ($chain, $znnToken, $qsrToken) {

            $znnBalance = $accountData['BalanceList'][$znnToken->token_standard] ?? 0;
            $qsrBalance = $accountData['BalanceList'][$qsrToken->token_standard] ?? 0;

            $account = Account::updateOrCreate([
                'chain_id' => $chain->id,
                'address' => $accountData['Address'],
            ], [
                'znn_balance' => $znnBalance,
                'znn_received' => $znnBalance,
                'genesis_znn_balance' => $znnBalance,
                'qsr_balance' => $qsrBalance,
                'qsr_received' => $qsrBalance,
                'genesis_qsr_balance' => $qsrBalance,
            ]);

            if ($znnBalance > 0) {
                $account->tokens()->attach($znnToken->id, [
                    'balance' => $znnBalance,
                    'updated_at' => $chain->created_at,
                ]);
            }

            if ($qsrBalance > 0) {
                $account->tokens()->attach($qsrToken->id, [
                    'balance' => $qsrBalance,
                    'updated_at' => $chain->created_at,
                ]);
            }
        });
    }
}
