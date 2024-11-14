<?php

declare(strict_types=1);

namespace App\Http\Controllers\Stats;

use App\Enums\Nom\AcceleratorProjectStatusEnum;
use App\Enums\Nom\EmbeddedContractsEnum;
use App\Models\Nom\AcceleratorProject;
use App\Models\Nom\Account;
use Asantibanez\LivewireCharts\Facades\LivewireCharts;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Number;
use MetaTags;

class AcceleratorZStatsController
{
    private string $defaultTab = 'overview';

    public function __invoke(?string $tab = null): View
    {
        MetaTags::title('Accelerator Z Stats')
            ->description('The Accelerator-Z Stats page shows an overview of the Accelerator Z embedded smart contract, projects, pillar voting engagement and contributors');

        $tab = $tab ?: $this->defaultTab;

        $data = null;
        if ($tab === 'overview') {
            $data = $this->getOverviewData();
        }

        return view('stats.accelerator-z', [
            'tab' => $tab,
            'data' => $data,
        ]);
    }

    private function getOverviewData(): array
    {
        $azContract = Account::firstWhere('address', EmbeddedContractsEnum::ACCELERATOR->value);
        $znnToken = app('znnToken');
        $qsrToken = app('qsrToken');

        $znnBalance = (float) filter_var($azContract->display_znn_balance, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
        $qsrBalance = (float) filter_var($azContract->display_qsr_balance, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
        $usdBalance = (float) filter_var($azContract->display_usd_balance, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);

        $znnPaid = $azContract->sentBlocks()
            ->where('token_id', $znnToken->id)
            ->sum('amount');
        $qsrPaid = $azContract->sentBlocks()
            ->where('token_id', $qsrToken->id)
            ->sum('amount');

        $znnPaid = $znnToken->getDisplayAmount($znnPaid);
        $qsrPaid = $qsrToken->getDisplayAmount($qsrPaid);

        $chartConfig = [
            'stroke' => [
                'show' => false,
            ],
            'legend' => [
                'show' => true,
            ],
            'dataLabels' => [
                'enabled' => true,
                'style' => [
                    'colors' => ['rgba(255, 255, 255, 0.80)'],
                ],
                'dropShadow' => [
                    'enabled' => false,
                ],
            ],
            'grid' => [
                'padding' => [
                    'bottom' => -180,
                ],
            ],
            'tooltip' => [
                'y' => [
                    'formatter' => '(val) => `$${val} million dollars baby!`',
                ],
            ],
            'plotOptions' => [
                'pie' => [
                    'expandOnClick' => false,
                    'donut' => [
                        'labels' => [
                            'show' => false,
                        ],
                    ],
                    'startAngle' => -90,
                    'endAngle' => 90,
                ],
                'donut' => [
                    'size' => 80,
                ],
            ],
        ];

        $znnDonutChart = LivewireCharts::pieChartModel()
            ->addSlice('Paid', $znnPaid, 'rgba(255, 255, 255, 0.20)')
            ->addSlice('Remaining', $znnBalance, config('zenon-hub.colours.zenon-green'))
            ->asDonut()
            ->setJsonConfig($chartConfig);

        $qsrDonutChart = LivewireCharts::pieChartModel()
            ->addSlice('Paid', $qsrPaid, 'rgba(255, 255, 255, 0.20)')
            ->addSlice('Remaining', $qsrBalance, config('zenon-hub.colours.zenon-blue'))
            ->asDonut()
            ->setJsonConfig($chartConfig);

        return [
            'znnBalance' => Number::abbreviate($znnBalance, 2),
            'qsrBalance' => Number::abbreviate($qsrBalance, 2),
            'usdBalance' => Number::abbreviate($usdBalance, 2),
            'znnPaid' => Number::abbreviate($znnPaid, 2),
            'qsrPaid' => Number::abbreviate($qsrPaid, 2),
            'completeProjects' => AcceleratorProject::where('status', AcceleratorProjectStatusEnum::COMPLETE->value)->count(),
            'znnDonutChart' => $znnDonutChart,
            'qsrDonutChart' => $qsrDonutChart,
        ];
    }
}
