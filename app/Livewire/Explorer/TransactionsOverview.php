<?php

declare(strict_types=1);

namespace App\Livewire\Explorer;

use App\Livewire\DateRangePickerTrait;
use App\Models\Nom\AccountBlock;
use Asantibanez\LivewireCharts\Facades\LivewireCharts;
use Asantibanez\LivewireCharts\Models\BaseChartModel;
use Illuminate\View\View;
use Livewire\Component;

class TransactionsOverview extends Component
{
    use DateRangePickerTrait;

    public function mount(): void
    {
        $this->timeframe = '7d';
        $this->endDate = now();
    }

    public function render(): View
    {
        $this->setDateRange();
        $chartModel = LivewireCharts::lineChartModel()
            ->setAnimated(true)
            ->setJsonConfig($this->getChartConfig());

        $chartData = $this->addChartData($chartModel);

        return view('livewire.explorer.transactions-overview', compact('chartData'), [
            'chartData' => $chartData,
            'dateRange' => $this->dateRange,
        ]);
    }

    private function getChartConfig(): array
    {
        return [
            'chart' => [
                'height' => '150px',
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
            ],
            'xaxis' => [
                'labels' => [
                    'show' => false,
                ],
                'axisBorder' => [
                    'show' => false,
                ],
                'axisTicks' => [
                    'show' => false,
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
        $data = AccountBlock::whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Prepare an associative array for quick lookup
        $dataByDate = $data->keyBy('date');

        // Loop through the date range
        foreach ($this->dateRange as $date) {
            $formattedDate = $date->format('Y-m-d');
            $count = $dataByDate->has($formattedDate) ? $dataByDate[$formattedDate]->count : 0;

            $chartModel->addPoint(
                $date->format('jS M Y'),
                $count,
                [
                    'tooltip' => sprintf('%s Transactions', number_format($count)),
                ]
            );
        }

        return $chartModel;
    }
}
