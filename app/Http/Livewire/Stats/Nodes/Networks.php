<?php

namespace App\Http\Livewire\Stats\Nodes;

use App\Http\Livewire\ChartTrait;
use Illuminate\Support\Facades\Cache;
use Livewire\Component;

class Networks extends Component
{
    use ChartTrait;

    public function render()
    {
        return view('livewire.stats.nodes.networks');
    }

    public function loadNetworksData()
    {
        $data = collect(Cache::get('node-networks', []))->sortDesc();

        $this->emit('stats.nodes.networksDataLoaded', [
            'labels' => $data->keys()->toArray(),
            'data' => $data->values()->toArray(),
        ]);
    }
}
