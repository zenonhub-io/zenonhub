<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Nom\Pillar;
use Illuminate\Contracts\View\View;
use MetaTags;

class PillarDetailController
{
    private string $defaultTab = 'all';

    public function __invoke(?string $slug = null, ?string $tab = 'delegators'): View
    {
        $pillar = Pillar::firstWhere('slug', $slug);

        if (! $pillar) {
            abort(404);
        }

        MetaTags::title('Zenon Network Pillars: Explore the Backbone of the Network of Momentum')
            ->description("Discover the complete list of Zenon Network's pillars and delve into essential statistics. Explore key data on weight, engagement, reward sharing, and network stability");

        return view('pillars.detail', [
            'tab' => $tab ?: $this->defaultTab,
            'pillar' => $pillar,
        ]);
    }
}
