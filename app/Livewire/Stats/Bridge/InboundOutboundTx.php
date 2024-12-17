<?php

declare(strict_types=1);

namespace App\Livewire\Stats\Bridge;

use App\Models\Nom\BridgeUnwrap;
use App\Models\Nom\BridgeWrap;
use Asantibanez\LivewireCharts\Facades\LivewireCharts;
use Asantibanez\LivewireCharts\Models\BaseChartModel;
use Illuminate\Support\Carbon;
use Illuminate\View\View;
use Livewire\Component;

class InboundOutboundTx extends Component
{
    public string $timeframe = '1y';

    public string $token = 'all';

    public Carbon $startDate;

    public Carbon $endDate;

    public function render(): View
    {
        $this->setDateRange();

        $pieChartModel = LivewireCharts::pieChartModel()
            ->setAnimated(true)
            ->setType('donut')
            ->setJsonConfig($this->getChartConfig());

        return view('livewire.stats.bridge.inbound-outbound-tx', [
            'pieChartModel' => $this->addChartData($pieChartModel),
        ]);
    }

    private function getChartConfig(): array
    {
        return [
            'chart' => [
                'height' => '450px',
            ],
            'stroke' => [
                'width' => 4,
                'colors' => ['#191818'],
            ],
            'legend' => [
                'show' => true,
                'position' => 'right',
                'labels' => [
                    'colors' => ['rgba(255, 255, 255, .8)'],
                ],
                'itemMargin' => [
                    'horizontal' => 8,
                    'vertical' => 8,
                ],
                'markers' => [
                    'width' => 16,
                    'height' => 16,
                    'radius' => 2,
                    'offsetY' => 4,
                    'offsetX' => -4,
                ],
            ],
            'dataLabels' => [
                'style' => [
                    'colors' => ['rgba(255, 255, 255, .8)'],
                ],
                'dropShadow' => [
                    'enabled' => false,
                ],
            ],
            'responsive' => [
                [
                    'breakpoint' => 576,
                    'options' => [
                        'chart' => [
                            'height' => 400,
                        ],
                        'stroke' => [
                            'width' => 2,
                        ],
                        'legend' => [
                            'markers' => [
                                'offsetY' => 0,
                                'offsetX' => -4,
                            ],
                        ],
                    ],
                ],
            ],
            'plotOptions' => [
                'pie' => [
                    'expandOnClick' => false,
                    'donut' => [
                        'size' => '45%',
                        'labels' => [
                            'show' => true,
                            'name' => [
                                'show' => true,
                                'color' => 'rgba(255, 255, 255, .8)',
                            ],
                            'total' => [
                                'show' => true,
                                'color' => 'rgba(255, 255, 255, .8)',
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }

    private function addChartData(BaseChartModel $pieChartModel): BaseChartModel
    {
        $inboundQuery = BridgeUnwrap::whereBetween('created_at', [$this->startDate, $this->endDate]);
        $outboundQuery = BridgeWrap::whereBetween('created_at', [$this->startDate, $this->endDate]);

        if (in_array($this->token, ['znn', 'qsr'])) {
            $tokenId = app("{$this->token}Token")->id;
            $inboundQuery->where('token_id', $tokenId);
            $outboundQuery->where('token_id', $tokenId);
        } else {
            $tokens = [
                app('znnToken')->id,
                app('qsrToken')->id,
            ];

            $inboundQuery->whereIn('token_id', $tokens);
            $outboundQuery->whereIn('token_id', $tokens);
        }

        $inbound = $inboundQuery->count();
        $outbound = $outboundQuery->count();
        $totalTransactions = $inbound + $outbound;
        $inboundPercentage = $this->calculatePercentage($inbound, $totalTransactions);
        $outboundPercentage = $this->calculatePercentage($outbound, $totalTransactions);

        return $pieChartModel->addSlice(__('Inbound'), $inbound, '#00CC88', ['tooltip' => $inboundPercentage . '%'])
            ->addSlice(__('Outbound'), $outbound, '#0099FF', ['tooltip' => $outboundPercentage . '%']);
    }

    private function setDateRange(): void
    {
        $this->startDate = match ($this->timeframe) {
            '1h' => now()->subHour(),
            '1d' => now()->subDay(),
            '1w' => now()->subWeek(),
            '1m' => now()->subMonth(),
            '1y' => now()->subYear(),
        };

        $this->endDate = now()->endOfDay();
    }

    private function calculatePercentage(int $partial, int $total): float
    {
        return $total > 0 ? round(($partial / $total) * 100, 2) : 0;
    }
}
