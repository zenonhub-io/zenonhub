<?php

namespace App\Http\Livewire\Charts;

use Cache;
use App\Http\Livewire\ChartTrait;
use Livewire\Component;

class NodeCountries extends Component
{
    use ChartTrait;

    public ?string $elementId;

    public function mount()
    {
        $this->loadData();
        $this->setData();
    }

    public function render()
    {
        return view('livewire.charts.tools.node-countries');
    }

    private function loadData()
    {
        $this->data = collect(Cache::get('node-countries', []))->sortDesc();
    }

    private function setData()
    {
        $total = $this->data->values()->sum();
        $this->labels = $this->data->map(function ($key, $value) use ($total){
            $percentage = ($key / $total) * 100;
            $percentage = number_format($percentage, 1);
            return "{$value} {$percentage}%";
        })->values()->toArray();

        $this->type = 'doughnut';
        $this->dataset = [
            [
                'label' => 'Nodes',
                'fill' => true,
                'data' => $this->data->values()->toArray(),
                //'backgroundColor' => 'rgba(1, 213, 87, 0.8)',
                //'hoverBackgroundColor' => 'rgba(1, 213, 87, 1)',
                //'hoverOffset' => -16,
            ],
        ];
    }
}
