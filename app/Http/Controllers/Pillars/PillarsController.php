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
        if ($tab === 'all') {
            $title = 'Pillars List: Explore the validator nodes of the Network of Momentum';
            $description = "Explore the complete list of Zenon Network's pillars, including their weight, engagement, reward sharing, and overall contribution to network stability";
            $canonical = route('pillar.list');
        } else {
            $title = sprintf('%s Pillars List: Explore the validator nodes of the Network of Momentum', str($tab)->singular()->title());
            $description = "Browse {$tab} pillars of the Zenon Network and explore weight, engagement, reward sharing, and their role in strengthening the Network of Momentum";
            $canonical = route('pillar.list', ['tab' => $tab]);
        }

        MetaTags::title($title)
            ->description($description)
            ->canonical($canonical)
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

        MetaTags::title("{$pillar->name} - Zenon Network Pillar Details")
            ->description("Delve into {$pillar->name}'s on-chain activity in the Zenon Network, including delegators, votes, reward engagement, and latest updates")
            ->canonical(route('pillar.detail', ['slug' => $pillar->slug]))
            ->metaByName('robots', 'index,follow');

        return view('pillars.detail', [
            'tab' => $tab,
            'pillar' => $pillar,
        ]);
    }
}
