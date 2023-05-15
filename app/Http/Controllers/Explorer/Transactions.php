<?php

namespace App\Http\Controllers\Explorer;

use App\Http\Controllers\PageController;
use App\Models\Nom\AccountBlock;

class Transactions extends PageController
{
    public function show()
    {
        $this->page['meta']['title'] = 'Transactions';
        $this->page['meta']['description'] = 'Transactions that have been confirmed on the Zenon Network. The list consists of transactions from sending and receiving tokens and the transactions for interacting with a smart contract';
        $this->page['data'] = [
            'component' => 'explorer.transactions',
        ];

        return $this->render('pages/explorer/overview');
    }

    public function detail($hash)
    {
        $transaction = AccountBlock::findByHash($hash);

        if (! $transaction) {
            abort(404);
        }

        $this->page['meta']['title'] = 'Transaction Detail';
        $this->page['meta']['description'] = "Detailed transaction info for hash {$transaction->hash}. The transaction status, block confirmation and token transfer are shown";
        $this->page['data'] = [
            'component' => 'explorer.transaction',
            'transaction' => $transaction,
        ];

        return $this->render('pages/explorer/detail');
    }
}
