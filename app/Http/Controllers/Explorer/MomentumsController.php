<?php

declare(strict_types=1);

namespace App\Http\Controllers\Explorer;

use App\Models\Nom\Momentum;
use Illuminate\Contracts\View\View;
use MetaTags;

class MomentumsController
{
    public function index(): View
    {
        MetaTags::title('Momentums')
            ->description('A list of the latest confirmed Momentums (blocks) on the Zenon Network. The timestamp, producer, number of transactions and hash are shown in the list')
            ->canonical(route('explorer.momentum.list'))
            ->metaByName('robots', 'index,nofollow');

        return view('explorer.momentum-list');
    }

    public function show(string $hash, ?string $tab = 'transactions'): View
    {
        $momentum = Momentum::where('hash', $hash)
            ->with('producerAccount', 'producerPillar')
            ->withCount('accountBlocks')
            ->first();

        if (! $momentum) {
            abort(404);
        }

        MetaTags::title(__('Momentum #:height details (:hash)', ['height' => $momentum->height, 'hash' => $momentum->hash]))
            ->description(__('Momentum #:height (:hash) detail page showing the network height, producing pillar and a list of transactions', ['height' => $momentum->height, 'hash' => $momentum->hash]))
            ->canonical(route('explorer.momentum.detail', ['hash' => $momentum->hash]))
            ->metaByName('robots', 'noindex,nofollow');

        return view('explorer.momentum-details', [
            'tab' => $tab,
            'momentum' => $momentum,
        ]);
    }
}
