<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Nom\Pillar;
use Illuminate\Contracts\View\View;
use MetaTags;

class PillarDetailController
{
    private string $defaultTab = 'delegators';

    public function __invoke(?string $slug = null, ?string $tab = null): View
    {
        $pillar = Pillar::firstWhere('slug', $slug);

        if (! $pillar) {
            abort(404);
        }

        MetaTags::title("{$pillar->name} - Pillar details")
            ->description("Explore the on-chain activity of {$pillar->name} in the Zenon Network. Discover information about its delegators, votes and updates");

        return view('pillars.detail', [
            'tab' => $tab ?: $this->defaultTab,
            'pillar' => $pillar,
        ]);
    }
}
