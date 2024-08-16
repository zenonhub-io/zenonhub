<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;
use MetaTags;

class PillarsController
{
    private string $defaultTab = 'all';

    public function __invoke(?string $tab = null): View
    {
        MetaTags::title('Zenon Network Pillars: Explore the Backbone of the Network of Momentum')
            ->description("Discover the complete list of Zenon Network's pillars and delve into essential statistics. Explore key data on weight, engagement, reward sharing, and network stability");

        return view('pillars', [
            'tab' => $tab ?: $this->defaultTab,
        ]);
    }
}
