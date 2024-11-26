<?php

declare(strict_types=1);

namespace App\Http\Controllers\Explorer;

use App\Models\Nom\Momentum;
use Illuminate\Contracts\View\View;
use MetaTags;

class MomentumDetailController
{
    private string $defaultTab = 'transactions';

    public function __invoke(string $hash, ?string $tab = null): View
    {
        $momentum = Momentum::where('hash', $hash)
            ->with('producerAccount', 'producerPillar')
            ->withCount('accountBlocks')
            ->first();

        if (! $momentum) {
            abort(404);
        }

        MetaTags::title(__('Momentum :height - Token details', ['height' => $momentum->height]))
            ->description(__('Momentum :height (:hash) detail page showing the network height, producing pillar and a list of transactions', ['height' => $momentum->height, 'hash' => $momentum->hash]));

        $tab = $tab ?: $this->defaultTab;

        return view('explorer.momentum-details', [
            'tab' => $tab,
            'momentum' => $momentum,
        ]);
    }
}
