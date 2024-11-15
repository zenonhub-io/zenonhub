<?php

declare(strict_types=1);

namespace App\Livewire\Explorer;

use App\Livewire\DateRangePickerTrait;
use App\Models\Nom\Account;
use Asantibanez\LivewireCharts\Facades\LivewireCharts;
use Asantibanez\LivewireCharts\Models\BaseChartModel;
use Illuminate\View\View;
use Livewire\Component;

class ActiveAddressesOverview extends Component
{
    use DateRangePickerTrait;

    public function mount(): void
    {
        $this->timeframe = '7d';
        $this->endDate = now()->subMonths(3);
    }

    public function render(): View
    {
        $this->setDateRange();
        $chartModel = LivewireCharts::columnChartModel()
            ->setAnimated(true)
            ->setJsonConfig($this->getChartConfig());

        $chartData = $this->addChartData($chartModel);

        return view('livewire.explorer.active-addresses-overview', compact('chartData'), [
            'chartData' => $chartData,
            'dateRange' => $this->dateRange,
        ]);
    }

    private function getChartConfig(): array
    {
        return [
            'chart' => [
                'height' => '200px',
            ],
            'colors' => [
                config('zenon-hub.colours.success'),
                config('zenon-hub.colours.info'),
            ],
            'legend' => [
                'show' => false,
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

    private function addChartData(BaseChartModel $chartModel): BaseChartModel
    {
        foreach ($this->dateRange as $date) {
            $chartModel->addColumn(
                $date->format('jS M'),
                Account::whereHas('sentBlocks', fn ($query) => $query->whereDate('created_at', '=', $date))->count(),
                config('zenon-hub.colours.info')
            );
        }

        return $chartModel;
    }
}
