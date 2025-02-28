<?php

declare(strict_types=1);

namespace App\Http\Controllers\Explorer;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;
use MetaTags;

class BridgeController
{
    public function __invoke(?string $tab = 'inbound'): View
    {
        $title = __(':tab Bridge Transactions: Track Transfers into the Zenon Network', ['tab' => Str::headline($tab)]);
        $description = __('View a detailed list of :tab bridge transactions, including sender and receiver addresses, amounts, and networks', ['tab' => $tab]);
        $canonical = route('explorer.bridge.list');

        if ($tab === 'outbound') {
            $title = __(':tab Bridge Transactions: Track Transfers from the Zenon Network', ['tab' => Str::headline($tab)]);
            $canonical = route('explorer.bridge.list', ['tab' => $tab]);
        } elseif ($tab === 'networks') {
            $title = __('Bridge Networks: Supported Chains and Wrapped Tokens');
            $description = __('Discover supported bridge networks, including their names, contract addresses, and total wrapped tokens on Zenon');
        }

        MetaTags::title($title)
            ->description($description)
            ->canonical($canonical)
            ->metaByName('robots', 'index,nofollow');

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
