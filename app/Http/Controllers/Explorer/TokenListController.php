<?php

declare(strict_types=1);

namespace App\Http\Controllers\Explorer;

use Illuminate\Contracts\View\View;
use MetaTags;

class TokenListController
{
    private string $defaultTab = 'all';

    public function __invoke(?string $tab = null): View
    {
        MetaTags::title('Tokens')
            ->description('The list of ZTS Tokens, their supply and the number of holders in the Network of Momentum');

        $tab = $tab ?: $this->defaultTab;

        return view('explorer.token-list', [
            'tab' => $tab,
        ]);
    }
}
