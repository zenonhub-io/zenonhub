<?php

namespace App\Http\Livewire\Stats\Bridge;

use Illuminate\Support\Facades\Http;
use Livewire\Component;

class Liquidity extends Component
{
    public function render()
    {
        $this->loadData();

        return view('livewire.stats.bridge.liquidity');
    }

    private function loadData()
    {

        $apiKey = 'GQSA17VIDYHSD8XTHEFJ2613JATDG9BCFT';

        $response = Http::get('https://api.etherscan.io/api', [
            'module' => 'account',
            'action' => 'balance',
            'address' => '0xb2e96a63479c2edd2fd62b382c89d5ca79f572d3',
            'tag' => 'latest',
            'apiKey' => $apiKey,
        ])->json();

        dd($response);
    }
}
