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
        $this->endDate = now();
    }

    public function render(): View
    {
        $this->setDateRange();
        $chartModel = LivewireCharts::lineChartModel()
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

        // Perform a single query to get the number of unique addresses with sent blocks grouped by date
        $data = Account::selectRaw('DATE(nom_account_blocks.created_at) as date, COUNT(DISTINCT nom_accounts.id) as count')
            ->join('nom_account_blocks', 'nom_accounts.id', '=', 'nom_account_blocks.account_id')
            ->whereBetween('nom_account_blocks.created_at', [$startDate, $endDate])
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
                    'tooltip' => sprintf('%s Active addresses', number_format($count)),
                ]
            );
        }

        return $chartModel;
    }
}
