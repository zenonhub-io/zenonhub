<?php

declare(strict_types=1);

namespace App\Livewire\Stats\Bridge;

use App\Livewire\DateRangePickerTrait;
use App\Models\Nom\BridgeStatHistory;
use App\Models\Nom\Token;
use Asantibanez\LivewireCharts\Facades\LivewireCharts;
use Asantibanez\LivewireCharts\Models\BaseChartModel;
use Illuminate\Support\Number;
use Illuminate\View\View;
use Livewire\Component;

class InboundOutboundVolume extends Component
{
    use DateRangePickerTrait;

    public string $token = 'znn';

    private ?int $inboundTx = null;

    private ?int $outboundTx = null;

    private ?int $inboundAmount = null;

    private ?int $outboundAmount = null;

    public function mount(): void
    {
        $this->timeframe = '7d';
        $this->endDate = now();
    }

    public function render(): View
    {
        $this->setDateRange();

        $token = app("{$this->token}Token");
        $chartModel = LivewireCharts::columnChartModel()
            ->setAnimated(true)
            ->multiColumn()
            ->setJsonConfig($this->getChartConfig());

        $chartData = $this->addChartData($chartModel, $token);
        $inboundAmount = $token->getDisplayAmount($this->inboundAmount);
        $outboundAmount = $token->getDisplayAmount($this->outboundAmount);
        $totalVolume = $inboundAmount + $outboundAmount;
        $netFlow = $inboundAmount - $outboundAmount;

        return view('livewire.stats.bridge.inbound-outbound-volume', [
            'chartData' => $chartData,
            'dateRange' => $this->dateRange,
            'inboundTx' => $this->inboundTx,
            'outboundTx' => $this->outboundTx,
            'inboundAmount' => Number::abbreviate($inboundAmount, 2),
            'outboundAmount' => Number::abbreviate($outboundAmount, 2),
            'totalVolume' => Number::abbreviate($totalVolume, 2),
            'netFlow' => Number::abbreviate($netFlow, 2),
        ]);
    }

    private function getChartConfig(): array
    {
        return [
            'chart' => [
                'height' => '450px',
            ],
            'colors' => [
                config('zenon-hub.colours.success'),
                config('zenon-hub.colours.info'),
            ],
            'legend' => [
                'show' => true,
                'position' => 'bottom',
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
                    'offsetY' => 0,
                    'offsetX' => -4,
                ],
            ],
            'tooltip' => [
                'theme' => 'dark',
            ],
            'yaxis' => [
                'show' => true,
            ],
        ];
    }

    private function addChartData(BaseChartModel $chartModel, Token $token): BaseChartModel
    {
        foreach ($this->dateRange as $date) {
            $query = BridgeStatHistory::where('token_id', $token->id);

            if ($this->timeframe === 'y') {
                $startDate = $this->getPeriodStart($date);
                $endDate = $this->getPeriodEnd($date);
                $query->whereBetween('date', [$startDate, $endDate]);
                $title = $date->format('M Y');
            } else {
                $query->whereDate('date', $date);
                $title = $date->format('jS M');
            }

            $data = $query->selectRaw('CAST(SUM(unwrapped_amount) AS SIGNED) as inbound_amount')
                ->selectRaw('CAST(SUM(wrapped_amount) AS SIGNED) as outbound_amount')
                ->selectRaw('CAST(SUM(unwrap_tx) AS SIGNED) as inbound_tx')
                ->selectRaw('CAST(SUM(wrap_tx) AS SIGNED) as outbound_tx')
                ->groupBy('token_id')
                ->first();

            $inbound = $data->inbound_amount ?? 0;
            $outbound = $data->outbound_amount ?? 0;

            $chartModel->addSeriesColumn(
                __('Inbound'),
                $title,
                $token->getDisplayAmount($inbound, 2, '.', ''),
            );

            $chartModel->addSeriesColumn(
                __('Outbound'),
                $title,
                $token->getDisplayAmount($outbound, 2, '.', ''),
            );

            $this->updateRunningTotals($data);
        }

        return $chartModel;
    }

    private function updateRunningTotals($data): void
    {
        $this->inboundTx += $data->inbound_tx ?? 0;
        $this->outboundTx += $data->outbound_tx ?? 0;
        $this->inboundAmount += $data->inbound_amount ?? 0;
        $this->outboundAmount += $data->outbound_amount ?? 0;
    }

    private function calculatePercentage(int|string $partial, int $total): float
    {
        return $total > 0 ? round(($partial / $total) * 100, 2) : 0;
    }
}
