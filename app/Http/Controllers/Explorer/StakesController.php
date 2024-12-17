<?php

declare(strict_types=1);

namespace App\Http\Controllers\Explorer;

use App\Models\Nom\Stake;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Number;
use MetaTags;

class StakesController
{
    public function __invoke(): View
    {
        MetaTags::title('Staking')
            ->description('A list of all staking entries for ZNN and ETH LP tokens on the Zenon Network, displayed by start timestamp in descending order');

        return view('explorer.stake-list', [
            'stats' => $this->getStats(),
        ]);
    }

    private function getStats(): array
    {
        $znnToken = app('znnToken');
        $query = Stake::whereActive()->where('token_id', $znnToken->id);

        $totalStaked = $query->sum('amount');
        $totalStaked = $znnToken->getDisplayAmount($totalStaked);

        $totalStakes = $query->count();
        $totalStakes = number_format($totalStakes);

        // TODO - need to duplicate the query, using this breaks the avg time below
        //        $totalStakers = $query->distinct('account_id')->count();
        //        $totalStakers = number_format($totalStakers);

        $avgDuration = $query->avg('duration');
        $endDate = now()->addSeconds((float) $avgDuration);
        $avgDuration = now()->diffInDays($endDate);

        return [
            'stakedTotal' => Number::abbreviate($totalStaked),
            //'stakersCount' => $totalStakers,
            'stakesCount' => $totalStakes,
            'avgDuration' => number_format($avgDuration),
        ];
    }
}
