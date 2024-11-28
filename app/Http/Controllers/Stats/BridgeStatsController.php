<?php

declare(strict_types=1);

namespace App\Http\Controllers\Stats;

use App\Services\BridgeStatus;
use Illuminate\Contracts\View\View;
use MetaTags;

class BridgeStatsController
{
    public function __invoke(?string $tab = 'overview'): View
    {
        MetaTags::title('Bridge Stats')
            ->description('The Bridge Stats page shows a detailed overview of the Multi-Chain Bridge including its status, admin actions, security info and supported networks');

        return view('stats.bridge', [
            'tab' => $tab,
            'status' => app(BridgeStatus::class),
            'affiliateLink' => config('nom.bridge.affiliateLink'),
        ]);
    }
}
