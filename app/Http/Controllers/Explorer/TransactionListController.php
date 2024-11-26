<?php

declare(strict_types=1);

namespace App\Http\Controllers\Explorer;

use Illuminate\Contracts\View\View;
use MetaTags;

class TransactionListController
{
    public function __invoke(?string $tab = null): View
    {
        MetaTags::title('Transactions')
            ->description('A list of transactions that have been confirmed on the Zenon Network. The list consists of transactions from sending and receiving tokens and the interactions with embedded smart contracts');

        return view('explorer.transaction-list');
    }
}
