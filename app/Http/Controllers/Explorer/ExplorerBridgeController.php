<?php

declare(strict_types=1);

namespace App\Http\Controllers\Explorer;

use Illuminate\Contracts\View\View;
use MetaTags;

class ExplorerBridgeController
{
    private string $defaultTab = 'inbound';

    public function __invoke(?string $tab = null): View
    {
        MetaTags::title('Bridge')
            ->description('A list of all staking entries for ZNN and ETH LP tokens on the Zenon Network, displayed by start timestamp in descending order');

        $tab = $tab ?: $this->defaultTab;

        return view('explorer.bridge', [
            'tab' => $tab,
            'stats' => match ($tab) {
                'inbound' => $this->getInboundStats(),
                'outbound' => $this->getOutboundStats(),
                'eth-lp' => $this->getEthLpStats(),
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

    private function getEthLpStats(): array
    {
        return [];
    }
}
