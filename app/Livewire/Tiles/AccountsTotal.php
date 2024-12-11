<?php

declare(strict_types=1);

namespace App\Livewire\Tiles;

use App\Livewire\DateRangePickerTrait;
use App\Models\Nom\NetworkStatHistory;
use Asantibanez\LivewireCharts\Facades\LivewireCharts;
use Asantibanez\LivewireCharts\Models\BaseChartModel;
use Illuminate\View\View;
use Livewire\Component;

class AccountsTotal extends Component
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
            ->setAnimated(true)
            ->setJsonConfig($this->getChartConfig());

        $chartData = $this->addChartData($chartModel);

        return view('livewire.tiles.accounts-total', compact('chartData'), [
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

        // Perform a single query to get the number of unique addresses with sent blocks grouped by date
        $data = NetworkStatHistory::selectRaw('total_addresses, DATE(date) as formatted_date')
            ->whereBetween('date', [$startDate, $endDate])
            ->orderBy('formatted_date')
            ->get();

        // Prepare an associative array for quick lookup
        $dataByDate = $data->keyBy('formatted_date');

        // Loop through the date range
        foreach ($this->dateRange as $date) {
            $formattedDate = $date->format('Y-m-d');
            $count = $dataByDate->has($formattedDate) ? (int) $dataByDate[$formattedDate]->total_addresses : 0;

            $chartModel->addColumn(
                $date->format('jS M Y'),
                $count,
                config('zenon-hub.colours.zenon-blue'),
                [
                    'tooltip' => sprintf('%s Total addresses', number_format($count)),
                ]
            );
        }

        return $chartModel;
    }
}
