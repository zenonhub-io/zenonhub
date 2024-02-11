<?php

namespace App\Http\Livewire\Stats\Nodes;

use App\Http\Livewire\ChartTrait;
use Illuminate\Support\Facades\Cache;
use Livewire\Component;

class Cities extends Component
{
    use ChartTrait;

    public function render()
    {
        return view('livewire.stats.nodes.cities');
    }

    public function loadCitiesData()
    {
        $data = collect(Cache::get('node-cities', []))->sortDesc();

        $this->emit('stats.nodes.citiesDataLoaded', [
            'labels' => $data->keys()->toArray(),
            'data' => $data->values()->toArray(),
        ]);
    }
}
