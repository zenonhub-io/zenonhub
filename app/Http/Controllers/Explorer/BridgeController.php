<?php

declare(strict_types=1);

namespace App\Http\Controllers\Explorer;

use App\Models\Nom\Stake;
use Illuminate\Contracts\View\View;
use MetaTags;

class BridgeController
{
    public function __invoke(?string $tab = 'inbound'): View
    {
        MetaTags::title('Bridge')
            ->description('A list of all incoming and outgoing bridge transactions and a list of LP providers, showing sender and receiver addresses, amount and network');

        return view('explorer.bridge-list', [
            'tab' => $tab,
            'stats' => match ($tab) {
                'inbound' => $this->getInboundStats(),
                'outbound' => $this->getOutboundStats(),
                'znn-eth-lp' => $this->getEthLpStats(),
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
        $znnEthLpToken = app('znnEthLpToken');
        $query = Stake::whereActive()->where('token_id', $znnEthLpToken->id);

        $totalStaked = $query->sum('amount');
        $totalStaked = $znnEthLpToken->getDisplayAmount($totalStaked);

        $totalStakes = $query->count();
        $totalStakes = number_format($totalStakes);

        $avgDuration = $query->avg('duration');
        $endDate = now()->addSeconds((float) $avgDuration);
        $avgDuration = now()->diffInDays($endDate);

        return [
            'stakedTotal' => $totalStaked,
            'stakesCount' => $totalStakes,
            'avgDuration' => number_format($avgDuration),
        ];
    }
}
