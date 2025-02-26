<?php

declare(strict_types=1);

namespace App\Http\Controllers\Explorer;

use App\Models\Nom\Plasma;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Number;
use MetaTags;

class PlasmaController
{
    public function __invoke(): View
    {
        MetaTags::title('Plasma Fusion: Active QSR Fusions in the Zenon Network')
            ->description('Discover all the addresses actively fusing QSR into Plasma on the Zenon Network, sorted by creation timestamp in descending order')
            ->canonical(route('explorer.plasma.list'))
            ->metaByName('robots', 'index,nofollow');

        return view('explorer.plasma-list', [
            'stats' => $this->getStats(),
        ]);
    }

    private function getStats(): array
    {
        return Cache::remember('explorer.plasma-list.stats', now()->addHour(), function () {
            $qsrToken = app('qsrToken');
            $totalPlasma = Plasma::whereActive()->sum('amount');
            $totalPlasma = $qsrToken->getDisplayAmount($totalPlasma);

            $avgAmount = Plasma::whereActive()->avg('amount');
            $avgAmount = round($avgAmount);
            $avgAmount = $qsrToken->getFormattedAmount($avgAmount, 2);

            $totalFusions = Plasma::whereActive()->count();
            $totalFusions = number_format($totalFusions);

            $totalFusers = Plasma::whereActive()->distinct('from_account_id')->count();
            $totalFusers = number_format($totalFusers);

            return [
                'plasmaTotal' => Number::abbreviate($totalPlasma),
                'avgAmount' => $avgAmount,
                'fusersCount' => $totalFusers,
                'fusionsCount' => $totalFusions,
            ];
        });
    }
}
