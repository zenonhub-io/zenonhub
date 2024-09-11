<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;
use MetaTags;

class AcceleratorZController
{
    private string $defaultTab = 'all';

    public function __invoke(?string $tab = null): View
    {
        MetaTags::title('Accelerator-Z Projects: Fueling Innovation in the Network of Momentum')
            ->description('Explore the diverse array of innovative projects funded by Accelerator-Z within the Network of Momentum ecosystem. A list of all Accelerator-Z projects showing their phases, votes and funding request.');

        return view('accelerator-z.list', [
            'tab' => $tab ?: $this->defaultTab,
        ]);
    }
}
