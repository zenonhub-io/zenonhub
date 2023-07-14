<?php

namespace App\Http\Livewire\Stats\Nodes;

use App\Http\Livewire\ChartTrait;
use Illuminate\Support\Facades\Cache;
use Livewire\Component;

class Countries extends Component
{
    use ChartTrait;

    public function render()
    {
        return view('livewire.stats.nodes.countries');
    }

    public function loadCountriesData()
    {
        $data = collect(Cache::get('node-countries', []))->sortDesc();

        $this->emit('stats.nodes.countriesDataLoaded', [
            'labels' => $data->keys()->toArray(),
            'data' => $data->values()->toArray(),
        ]);
    }
}
