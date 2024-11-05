<?php

declare(strict_types=1);

namespace App\Http\Controllers\Stats;

use Illuminate\Contracts\View\View;
use MetaTags;

class PublicNodesStatsController
{
    private string $defaultTab = 'overview';

    public function __invoke(?string $tab = null): View
    {
        MetaTags::title('Zenon Node Stats')
            ->description('Our Public node stats page displays the Zenon Network public RPC node stats including their geographic distribution, version and network data');

        return view('stats.nodes', [
            'tab' => $tab ?: $this->defaultTab,
        ]);
    }
}
