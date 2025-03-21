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
            $title = __('Pillars List: Explore the Validator Nodes of the Network of Momentum');
            $description = __("Explore the complete list of Zenon Network's pillars, including their weight, engagement, reward sharing, and overall contribution to network stability");
            $canonical = route('pillar.list');
        } else {
            $title = __(':tab Pillars List: Explore the Validator Nodes of the Network of Momentum', ['tab' => str($tab)->singular()->title()]);
            $description = __('Browse :tab pillars of the Zenon Network and explore weight, engagement, reward sharing, and their role in strengthening the Network of Momentum', ['tab' => $tab]);
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

        MetaTags::title(__(':name - Zenon Network Pillar Details', ['name' => $pillar->name]))
            ->description(__("Delve into :name's on-chain activity in the Zenon Network, including delegators, votes, rewards and engagement", ['name' => $pillar->name]))
            ->canonical(route('pillar.detail', ['slug' => $pillar->slug]))
            ->metaByName('robots', 'index,follow');

        return view('pillars.detail', [
            'tab' => $tab,
            'pillar' => $pillar,
        ]);
    }
}
