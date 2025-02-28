<?php

declare(strict_types=1);

namespace App\Http\Controllers\Stats;

use App\Enums\Nom\AcceleratorProjectStatusEnum;
use App\Enums\Nom\EmbeddedContractsEnum;
use App\Models\Nom\AcceleratorProject;
use App\Models\Nom\Account;
use App\Models\Nom\Pillar;
use Asantibanez\LivewireCharts\Facades\LivewireCharts;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Number;
use MetaTags;

class AcceleratorZStatsController
{
    public function __invoke(?string $tab = 'overview'): View
    {
        MetaTags::title(__('Accelerator-Z Stats: Projects, Voting, and Contributor Insights'))
            ->description(__('Get detailed statistics on the Accelerator-Z embedded smart contract, including project progress, pillar voting engagement, and contributor activity'))
            ->canonical(route('stats.accelerator-z'))
            ->metaByName('robots', 'index,nofollow');

        return view('stats.accelerator-z', [
            'tab' => $tab,
            'stats' => match ($tab) {
                'overview' => $this->getOverviewStats(),
                'engagement' => $this->getEngagementStats(),
                'contributors' => $this->getContributorStats(),
                'default' => null
            },
        ]);
    }

    private function getOverviewStats(): array
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

    private function getEngagementStats(): array
    {
        $totalProjects = AcceleratorProject::count();
        $acceptedProjects = AcceleratorProject::whereIn('status', [
            AcceleratorProjectStatusEnum::ACCEPTED->value,
            AcceleratorProjectStatusEnum::COMPLETE->value,
        ])->count();

        $acceptedProjectsPercentage = ($acceptedProjects / $totalProjects) * 100;

        $votingPillars = Pillar::whereActive()->whereHas('votes')->count();
        $totalPillars = Pillar::whereActive()->count();
        $votingPillarPercentage = ($votingPillars / $totalPillars) * 100;

        $avgVoteTime = Pillar::avg('az_avg_vote_time');

        return [
            'votingPillars' => $votingPillars,
            'percentageVotingPillars' => round($votingPillarPercentage),
            'avgVoteTime' => now()->subSeconds($avgVoteTime)->diffForHumans(['parts' => 2], true),
            'percentageAcceptedProjects' => round($acceptedProjectsPercentage),
        ];
    }

    private function getContributorStats(): array
    {
        $znnToken = app('znnToken');
        $qsrToken = app('qsrToken');
        $znnPaid = AcceleratorProject::whereCompleted()->sum('znn_requested');
        $qsrPaid = AcceleratorProject::whereCompleted()->sum('qsr_requested');

        return [
            'totalContributors' => Account::whereHas('projects')->count(),
            'completeProjects' => AcceleratorProject::whereCompleted()->count(),
            'znnPaid' => $znnToken->getFormattedAmount($znnPaid),
            'qsrPaid' => $qsrToken->getFormattedAmount($qsrPaid),
        ];
    }
}
