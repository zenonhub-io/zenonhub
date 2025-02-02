<?php

declare(strict_types=1);

namespace App\Http\Controllers\Explorer;

use Illuminate\Contracts\View\View;
use MetaTags;

class BridgeController
{
    public function __invoke(?string $tab = 'inbound'): View
    {
        MetaTags::title('Bridge')
            ->description('A list of all incoming and outgoing bridge transactions and a list of LP providers, showing sender and receiver addresses, amount and network')
            ->meta([
                'robots' => 'index,follow',
                'canonical' => route('explorer.bridge', ['tab' => $tab]),
            ]);

        return view('explorer.bridge-list', [
            'tab' => $tab,
            'stats' => match ($tab) {
                'inbound' => $this->getInboundStats(),
                'outbound' => $this->getOutboundStats(),
                'networks' => $this->getNetworkStats(),
            },
        ]);
    }

    private function getInboundStats(): array
    {
        return [];
    }

    private function getOutboundStats(): array
    {
        return [];
    }

    private function getNetworkStats(): array
    {
        return [];
    }
}
