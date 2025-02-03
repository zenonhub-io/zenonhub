<?php

declare(strict_types=1);

namespace App\Http\Controllers\Pillars;

use App\Models\Nom\Pillar;
use Illuminate\Contracts\View\View;
use MetaTags;

class PillarsController
{
    public function index(?string $tab = 'all'): View
    {
        MetaTags::title('Zenon Network Pillars: Explore the Backbone of the Network of Momentum')
            ->description("Discover the complete list of Zenon Network's pillars and delve into essential statistics. Explore key data on weight, engagement, reward sharing, and network stability")
            ->canonical(route('pillar.list', ['tab' => $tab]))
            ->metaByName('robots', 'index,follow');

        return view('pillars.list', [
            'tab' => $tab,
        ]);
    }

    public function show(?string $slug = null, ?string $tab = 'delegators'): View
    {
        $pillar = Pillar::firstWhere('slug', $slug);

        if (! $pillar) {
            abort(404);
        }

        MetaTags::title("{$pillar->name} - Pillar details")
            ->description("Explore the on-chain activity of {$pillar->name} in the Zenon Network. Discover information about its delegators, votes and updates")
            ->canonical(route('pillar.detail', ['slug' => $pillar->slug]))
            ->metaByName('robots', 'index,nofollow');

        return view('pillars.detail', [
            'tab' => $tab,
            'pillar' => $pillar,
        ]);
    }
}
