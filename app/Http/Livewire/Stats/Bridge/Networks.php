<?php

namespace App\Http\Livewire\Stats\Bridge;

use App\Services\ZenonSdk;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
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
        $cacheKey = 'nom.bridgeStats.getAllNetworks';

        try {
            $znn = App::make(ZenonSdk::class);
            $data = $znn->bridge->getAllNetworks()['data']->list;
            Cache::forever($cacheKey, $data);
        } catch (\Throwable $throwable) {
            $data = Cache::get($cacheKey);
        }

        $this->networkInfo = $data;
    }
}
