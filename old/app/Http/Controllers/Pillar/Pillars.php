<?php

declare(strict_types=1);

namespace App\Http\Controllers\Pillar;

use App\Domains\Nom\Models\Pillar;
use Meta;

class Pillars
{
    public function show()
    {
        Meta::title('Zenon Network Pillars: Explore the Backbone of the Network of Momentum')
            ->description("Discover the complete list of Zenon Network's pillars and delve into essential statistics. Explore key data on weight, engagement, reward sharing, and network stability");

        return view('pages/pillars/overview');
    }

    public function detail($slug)
    {
        $pillar = Pillar::findBy('slug', $slug);

        if (! $pillar) {
            abort(404);
        }

        Meta::title("{$pillar->name} - Pillar details")
            ->description("Explore the on-chain activity of {$pillar->name} in the Zenon Network. Discover information about its delegators, votes and updates");

        return view('pages/pillars/pillar', [
            'pillar' => $pillar,
        ]);
    }
}
