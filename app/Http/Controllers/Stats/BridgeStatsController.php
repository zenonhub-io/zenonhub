<?php

declare(strict_types=1);

namespace App\Http\Controllers\Stats;

use App\Enums\Nom\AccountRewardTypesEnum;
use App\Models\Nom\Account;
use App\Models\Nom\AccountReward;
use App\Services\BridgeStatus;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Number;
use MetaTags;

class BridgeStatsController
{
    public function __invoke(?string $tab = 'overview'): View
    {
        MetaTags::title(__('Bridge Stats: Multi-Chain Bridge Status & Insights'))
            ->description(__('Explore detailed insights on the Multi-Chain Bridge, including current status, admin actions, security details, orchestrator details and affiliate leaderboard'))
            ->canonical(route('stats.bridge'))
            ->metaByName('robots', 'index,nofollow');

        return view('stats.bridge', [
            'tab' => $tab,
            'status' => app(BridgeStatus::class),
            'stats' => match ($tab) {
                'affiliates' => $this->getAffiliateStats(),
                default => [],
            },
        ]);
    }

    private function getAffiliateStats(): array
    {
        return Cache::remember('stats.bridge.affiliates', now()->addHour(), function () {
            $znnToken = app('znnToken');
            $qsrToken = app('qsrToken');

            $znnPaid = AccountReward::where('type', AccountRewardTypesEnum::BRIDGE_AFFILIATE)
                ->where('token_id', $znnToken->id)
                ->sum('amount');

            $qsrPaid = AccountReward::where('type', AccountRewardTypesEnum::BRIDGE_AFFILIATE)
                ->where('token_id', $qsrToken->id)
                ->sum('amount');

            $totalAffiliates = Account::whereRelation('rewards', 'type', AccountRewardTypesEnum::BRIDGE_AFFILIATE)->count();

            return [
                'znn_paid' => Number::abbreviate($znnToken->getDisplayAmount($znnPaid), 2),
                'qsr_paid' => Number::abbreviate($znnToken->getDisplayAmount($qsrPaid), 2),
                'total_affiliates' => $totalAffiliates,
            ];
        });
    }
}
