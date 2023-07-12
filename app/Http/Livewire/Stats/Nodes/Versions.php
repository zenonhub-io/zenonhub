<?php

namespace App\Http\Livewire\Stats\Nodes;

use App\Http\Livewire\ChartTrait;
use Illuminate\Support\Facades\Cache;
use Livewire\Component;

class Versions extends Component
{
    use ChartTrait;

    public bool $readyToLoad = false;

    public function render()
    {
        return view('livewire.stats.nodes.versions');
    }

    public function loadVersionsData()
    {
        $this->readyToLoad = true;

        $data = collect(Cache::get('node-versions', []))->sortDesc();

        $this->emit('stats.nodes.versionsDataLoaded', [
            'labels' => $data->keys()->toArray(),
            'data' => $data->values()->toArray(),
        ]);
    }
}
