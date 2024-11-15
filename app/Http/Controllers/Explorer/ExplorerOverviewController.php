<?php

declare(strict_types=1);

namespace App\Http\Controllers\Explorer;

use App\Models\Nom\Account;
use App\Models\Nom\AccountBlock;
use App\Models\Nom\Momentum;
use App\Models\Nom\Token;
use Illuminate\Contracts\View\View;
use MetaTags;

class ExplorerOverviewController
{
    public function __invoke(): View
    {
        MetaTags::title('Zenon Hub | Explore the Zenon Network Blockchain with Ease', false);

        return view('explorer/overview', [
            'stats' => [
                'momentums' => number_format(Momentum::count()),
                'transactions' => number_format(AccountBlock::count()),
                'addresses' => number_format(Account::count()),
                'tokens' => number_format(Token::count()),
            ],
        ]);
    }
}
