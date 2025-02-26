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
        MetaTags::title('Latest Momentums: Confirmed Blocks in the Zenon Network')
            ->description('Browse the latest confirmed Momentums (blocks) on the Zenon Network, including timestamps, producers, transaction counts, and hashes')
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

        MetaTags::title(__('Momentum #:height Details: Hash :hash', ['height' => $momentum->height, 'hash' => short_hash($momentum->hash)]))
            ->description(__('View details for Momentum #:height (:hash), including the network height, producing pillar, and transaction list', ['height' => $momentum->height, 'hash' => $momentum->hash]))
            ->canonical(route('explorer.momentum.detail', ['hash' => $momentum->hash]))
            ->metaByName('robots', 'noindex,nofollow');

        return view('explorer.momentum-details', [
            'tab' => $tab,
            'momentum' => $momentum,
        ]);
    }
}
