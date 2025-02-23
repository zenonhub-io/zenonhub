<?php

declare(strict_types=1);

namespace App\Http\Controllers\Explorer;

use App\Models\Nom\AccountBlock;
use Illuminate\Contracts\View\View;
use MetaTags;

class TransactionsController
{
    public function index(): View
    {
        MetaTags::title('Transactions')
            ->description('A list of transactions that have been confirmed on the Zenon Network. The list consists of transactions from sending and receiving tokens and the interactions with embedded smart contracts')
            ->canonical(route('explorer.transaction.list'))
            ->metaByName('robots', 'index,nofollow');

        return view('explorer.transaction-list');
    }

    public function show(string $hash, ?string $tab = 'data'): View
    {
        $transaction = AccountBlock::where('hash', $hash)
            ->with('account', 'toAccount', 'token', 'momentum', 'parent', 'pairedAccountBlock', 'data')
            ->withCount('descendants')
            ->first();

        if (! $transaction) {
            abort(404);
        }

        MetaTags::title(__('Transaction details (:hash)', ['hash' => $transaction->hash]))
            ->description(__('Detailed transaction info for hash :hash. The transaction status, block type, confirmation and token transfer are shown', ['hash' => $transaction->hash]))
            ->canonical(route('explorer.transaction.detail', ['hash' => $transaction->hash]))
            ->metaByName('robots', 'noindex,nofollow');

        return view('explorer.transaction-details', [
            'tab' => $tab,
            'transaction' => $transaction,
        ]);
    }
}
