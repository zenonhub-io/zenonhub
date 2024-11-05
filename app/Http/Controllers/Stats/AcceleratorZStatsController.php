<?php

declare(strict_types=1);

namespace App\Http\Controllers\Stats;

use Illuminate\Contracts\View\View;
use MetaTags;

class AcceleratorZStatsController
{
    private string $defaultTab = 'overview';

    public function __invoke(?string $tab = null): View
    {
        MetaTags::title('Accelerator Z Stats')
            ->description('The Accelerator-Z Stats page shows an overview of the Accelerator Z embedded smart contract, projects, pillar voting engagement and contributors');

        return view('stats.accelerator-z', [
            'tab' => $tab ?: $this->defaultTab,
        ]);
    }
}
