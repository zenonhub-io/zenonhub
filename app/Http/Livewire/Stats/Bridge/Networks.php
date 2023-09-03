<?php

namespace App\Http\Livewire\Stats\Bridge;

use Illuminate\Support\Facades\App;
use Livewire\Component;

class Networks extends Component
{
    public array $networkInfo;

    public function render()
    {
        return view('livewire.stats.bridge.networks');
    }

    public function loadNetworkData()
    {
        $znn = App::make('zenon.api');
        $this->networkInfo = $znn->bridge->getAllNetworks()['data']->list;
    }
}
