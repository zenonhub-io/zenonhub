<?php

declare(strict_types=1);

namespace App\Http\Controllers\Explorer;

use App\Models\Nom\Stake;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Number;
use MetaTags;

class StakesController
{
    public function __invoke(?string $tab = 'znn'): View
    {
        if ($tab === 'znn') {
            $title = __(':znn Staking Entries: All Accounts Staking :znn Tokens', ['znn' => app('znnToken')->symbol]);
            $description = __('Explore all active :znn staking entries on the Zenon Network, listed in descending order by start timestamp. View total staked amounts and staking durations', ['znn' => app('znnToken')->symbol]);
            $canonical = route('explorer.stake.list');
        } else {
            $title = __(':znnethlp Staking Entries: All Accounts Staking :znnethlp Tokens', ['znnethlp' => app('znnEthLpToken')->symbol]);
            $description = __('Discover all :znnethlp staking entries on the Zenon Network, sorted by start timestamp. Gain insights into staking amounts, durations, and participants', ['znnethlp' => app('znnEthLpToken')->symbol]);
            $canonical = route('explorer.stake.list', ['tab' => $tab]);
        }

        MetaTags::title($title)
            ->description($description)
            ->canonical($canonical)
            ->metaByName('robots', 'index,nofollow');

        $token = match ($tab) {
            'znn' => app('znnToken'),
            'znn-eth-lp' => app('znnEthLpToken'),
        };

        return view('explorer.stake-list', [
            'tab' => $tab,
            'token' => $token,
            'stats' => $this->getStats($token),
        ]);
    }

    private function getStats($token): array
    {
        return Cache::remember('explorer.stakes-list.stats-' . $token->id, now()->addHour(), function () use ($token) {
            $totalStaked = Stake::whereActive()->where('token_id', $token->id)->sum('amount');
            $totalStaked = $token->getDisplayAmount($totalStaked);

            $avgDuration = Stake::whereActive()->where('token_id', $token->id)->avg('duration');
            $endDate = now()->addSeconds((float) $avgDuration);
            $avgDuration = now()->diffInDays($endDate);

            $totalStakes = Stake::whereActive()->where('token_id', $token->id)->count();
            $totalStakes = number_format($totalStakes);

            $totalStakers = Stake::whereActive()->where('token_id', $token->id)->distinct('account_id')->count();
            $totalStakers = number_format($totalStakers);

            return [
                'stakedTotal' => $totalStaked > 1 ? Number::abbreviate($totalStaked) : $totalStaked,
                'avgDuration' => number_format($avgDuration),
                'stakersCount' => $totalStakers,
                'stakesCount' => $totalStakes,
            ];
        });
    }
}
