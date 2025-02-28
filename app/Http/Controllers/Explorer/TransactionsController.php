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
        MetaTags::title(__('Zenon Network Transactions: Confirmed Transfers & Smart Contract Interactions'))
            ->description(__('Browse confirmed transactions on the Zenon Network, including token transfers and embedded smart contract interactions'))
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

        MetaTags::title(__('Transaction Details: Hash :hash', ['hash' => short_hash($transaction->hash)]))
            ->description(__('View detailed information for transaction :hash, including status, block type, confirmation, and token transfer data', ['hash' => $transaction->hash]))
            ->canonical(route('explorer.transaction.detail', ['hash' => $transaction->hash]))
            ->metaByName('robots', 'noindex,nofollow');

        return view('explorer.transaction-details', [
            'tab' => $tab,
            'transaction' => $transaction,
        ]);
    }
}
