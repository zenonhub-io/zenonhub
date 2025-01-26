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
        MetaTags::title('Plasma (Fused QSR)')
            ->description('A list of all the addresses in the Zenon Network actively fusing QSR into plasma sorted by creation timestamp in descending order');

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
