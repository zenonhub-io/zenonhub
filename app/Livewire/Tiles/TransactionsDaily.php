<?php

declare(strict_types=1);

namespace App\Livewire\Tiles;

use App\Livewire\DateRangePickerTrait;
use App\Models\Nom\AccountBlock;
use Asantibanez\LivewireCharts\Facades\LivewireCharts;
use Asantibanez\LivewireCharts\Models\BaseChartModel;
use Illuminate\View\View;
use Livewire\Component;

class TransactionsDaily extends Component
{
    use DateRangePickerTrait;

    public function mount(): void
    {
        $this->endDate = now();
    }

    public function render(): View
    {
        $this->setDateRange();
        $chartModel = LivewireCharts::columnChartModel()
            ->setJsonConfig($this->getChartConfig());

        $chartData = $this->addChartData($chartModel);

        return view('livewire.tiles.transactions-daily', compact('chartData'), [
            'chartData' => $chartData,
            'dateRange' => $this->dateRange,
        ]);
    }

    private function getChartConfig(): array
    {
        return [
            'chart' => [
                'height' => '257px',
            ],
            'stroke' => [
                'curve' => 'smooth',
            ],
            'grid' => [
                'show' => false,
            ],
            'legend' => [
                'show' => false,
            ],
            'tooltip' => [
                'theme' => 'dark',
            ],
            'yaxis' => [
                'show' => true,
                'axisBorder' => [
                    'show' => true,
                    'color' => config('zenon-hub.colours.bg-dark'),
                ],
                'axisTicks' => [
                    'show' => true,
                    'color' => config('zenon-hub.colours.bg-dark'),
                ],
            ],
            'xaxis' => [
                'labels' => [
                    'show' => false,
                ],
                'axisBorder' => [
                    'show' => true,
                    'color' => config('zenon-hub.colours.bg-dark'),
                ],
                'axisTicks' => [
                    'show' => true,
                    'color' => config('zenon-hub.colours.bg-dark'),
                ],
            ],
        ];
    }

    private function addChartData(BaseChartModel $chartModel): BaseChartModel
    {
        $startDate = $this->dateRange->first()->startOfDay();
        $endDate = $this->dateRange->last()->endOfDay();

        // Perform a single query to get count account block grouped by date
        $data = AccountBlock::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Prepare an associative array for quick lookup
        $dataByDate = $data->keyBy('date');

        // Loop through the date range
        foreach ($this->dateRange as $date) {
            $formattedDate = $date->format('Y-m-d');
            $count = $dataByDate->has($formattedDate) ? $dataByDate[$formattedDate]->count : 0;

            $chartModel->addColumn(
                $date->format('jS M Y'),
                $count,
                config('zenon-hub.colours.zenon-blue'),
                [
                    'tooltip' => sprintf('%s Transactions', number_format($count)),
                ]
            );
        }

        return $chartModel;
    }
}
