<?php

declare(strict_types=1);

namespace App\Http\Controllers\Explorer;

use App\Models\Nom\Account;
use App\Models\Nom\AccountBlock;
use App\Models\Nom\Momentum;
use App\Models\Nom\Token;
use Illuminate\Contracts\View\View;
use MetaTags;

class OverviewController
{
    public function __invoke(): View
    {
        MetaTags::title(__('Zenon Network Overview: Momentums, Blocks, Accounts, & Tokens'))
            ->description(__('Explore the Zenon Network through its momentums, blocks, accounts, tokens, and more on Zenon Hub'))
            ->canonical(route('explorer.overview'))
            ->metaByName('robots', 'index,nofollow');

        return view('explorer.overview', [
            'stats' => $this->getStats(),
        ]);
    }

    private function getStats(): array
    {
        return [
            'momentums' => number_format(Momentum::max('height')),
            'blocks' => number_format(AccountBlock::count()),
            'addresses' => number_format(Account::count()),
            'tokens' => number_format(Token::count()),
        ];
    }
}
