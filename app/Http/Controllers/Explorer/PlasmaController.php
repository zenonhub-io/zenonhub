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
        MetaTags::title(__('Plasma Fusion: Active :qsr Fusions in the Zenon Network', ['qsr' => app('qsrToken')->symbol]))
            ->description(__('Discover all the addresses actively fusing :qsr into Plasma on the Zenon Network, sorted by creation timestamp in descending order', ['qsr' => app('qsrToken')->symbol]))
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
