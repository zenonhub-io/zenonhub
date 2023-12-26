<?php

namespace App\Http\Livewire\Stats\Bridge;

use App\Models\Nom\BridgeAdmin;
use App\Services\BitQuery;
use App\Services\ZenonSdk;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Number;
use Livewire\Component;

class Overview extends Component
{
    public ?array $liquidityData;

    public ?array $holders;

    public function render()
    {
        $znn = App::make(ZenonSdk::class);
        $bridgeStats = $znn->bridge->getBridgeInfo()['data'];
        $adminAccount = BridgeAdmin::getActiveAdmin()->account;
        $orchestrators = Cache::get('orchestrators-online-percentage');

        return view('livewire.stats.bridge.overview', [
            'adminAddress' => $adminAccount,
            'halted' => $bridgeStats->halted,
            'orchestrators' => number_format($orchestrators),
            'affiliateLink' => config('zenon.bridge.affiliate_link'),
        ]);
    }

    public function loadOverviewData(): void
    {
        $this->loadLiquidityData();
    }

    private function loadLiquidityData(): void
    {
        $bitQuery = App::make(BitQuery::class);
        $data = $bitQuery->getLiquidityData();

        $poolData = collect($data['address'][0]['balances']);
        $pooledZnn = $poolData->where('currency.symbol', 'wZNN')->pluck('value')->first();
        $pooledEth = $poolData->where('currency.symbol', 'WETH')->pluck('value')->first();
        $pooledZnnValue = ($pooledZnn * znn_price());
        $pooledEthValue = ($pooledEth * eth_price());
        $totalLiquidity = ($pooledZnnValue + $pooledEthValue);

        $znnFormatter = $ethFormatter = $liquidityFormatter = 'format';

        if ($totalLiquidity > 100000) {
            $liquidityFormatter = 'abbreviate';
        }

        if ($pooledZnn > 10000) {
            $znnFormatter = 'abbreviate';
        }

        if ($pooledEth > 10000) {
            $ethFormatter = 'abbreviate';
        }

        $this->liquidityData = [
            'totalLiquidity' => Number::{$liquidityFormatter}($totalLiquidity, 2),
            'pooledWznn' => Number::{$znnFormatter}($pooledZnn, 2),
            'pooledWeth' => Number::{$ethFormatter}($pooledEth, 2),
        ];
    }
}
