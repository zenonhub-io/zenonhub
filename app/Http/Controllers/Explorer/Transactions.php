<?php

namespace App\Http\Controllers\Explorer;

use App\Models\Nom\AccountBlock;
use Meta;

class Transactions
{
    public function show()
    {
        Meta::title('Zenon Transaction Information')
            ->description('Transactions that have been confirmed on the Zenon Network. The list consists of transactions from sending and receiving tokens and the interactions with embedded smart contracts');

        return view('pages/explorer/overview', [
            'view' => 'explorer.transactions',
        ]);
    }

    public function detail($hash)
    {
        $transaction = AccountBlock::findByHash($hash);

        if (! $transaction) {
            abort(404);
        }

        Meta::title('Zenon Transaction Details')
            ->description("Detailed transaction info for hash {$transaction->hash}. The transaction status, block type, confirmation and token transfer are shown");

        return view('pages/explorer/detail', [
            'view' => 'explorer.transaction',
            'transaction' => $transaction,
        ]);
    }
}
