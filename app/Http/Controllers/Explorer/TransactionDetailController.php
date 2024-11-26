<?php

declare(strict_types=1);

namespace App\Http\Controllers\Explorer;

use App\Models\Nom\AccountBlock;
use Illuminate\Contracts\View\View;
use MetaTags;

class TransactionDetailController
{
    private string $defaultTab = 'data';

    public function __invoke(string $hash, ?string $tab = null): View
    {
        $transaction = AccountBlock::where('hash', $hash)
            ->with('account', 'toAccount', 'token', 'momentum', 'parent', 'pairedAccountBlock')
            ->withCount('descendants')
            ->first();

        if (! $transaction) {
            abort(404);
        }

        MetaTags::title(__('Transaction details (:hash)', ['hash' => $transaction->hash]))
            ->description(__('Detailed transaction info for hash :hash. The transaction status, block type, confirmation and token transfer are shown', ['hash' => $transaction->hash]));

        $tab = $tab ?: $this->defaultTab;

        return view('explorer.transaction-details', [
            'tab' => $tab,
            'transaction' => $transaction,
        ]);
    }
}
