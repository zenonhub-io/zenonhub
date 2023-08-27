<?php

namespace App\Http\Livewire\Stats\Bridge;

use App\Models\Nom\Account;
use DigitalSloth\ZnnPhp\Zenon;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Livewire\Component;

class Overview extends Component
{
    private Zenon $znn;

    public ?array $liquidityData;

    public ?array $holders;

    public ?string $affiliateLink;

    public function __construct($id = null)
    {
        parent::__construct($id);
        $this->znn = App::make('zenon.api');
    }

    public function render()
    {
        $this->affiliateLink = config('zenon.bridge_affiliate_link');
        $bridgeStats = $this->znn->bridge->getBridgeInfo()['data'];

        return view('livewire.stats.bridge.overview', [
            'adminAddress' => Account::findByAddress($bridgeStats->administrator),
            'halted' => $bridgeStats->halted,
            'orchestratorsOnline' => Cache::get('orchestrators-online-percentage'),
        ]);
    }

    public function loadLiquidityData()
    {
        // Orbital Staked ETH = (Orbital’s Balance of ETH-wZNN LP ZTS) / (Total Supply of ETH-wZNN LP ERC20) * (Amount of ETH in the ETH-wZNN Pool)

        // https://github.com/Uniswap/v2-subgraph/blob/master/schema.graphql
        $query = <<<'GQL'
{
 pair(id: "0xdac866a3796f85cb84a914d98faec052e3b5596d"){
  token0 {
  	id
    symbol
    name
    totalLiquidity
    derivedETH
  }
  token1 {
  	id
    symbol
    name
    totalLiquidity
    derivedETH
  }
  id
  reserveUSD
  reserve0
  reserve1
 }
}
GQL;

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
        ])->post('https://api.thegraph.com/subgraphs/name/ianlapham/uniswap-v2-dev', [
            'query' => $query,
        ])->json('data.pair');

        $this->liquidityData = [
            'totalLiquidity' => number_format($response['reserveUSD'], 2),
            'pooledWznn' => number_format($response['reserve0'], 2),
            'pooledWeth' => number_format($response['reserve1'], 2),
            'pairId' => $response['id'],
            'wznnId' => $response['token0']['id'],
        ];

        // total supply & holders
        // https://github.com/EverexIO/Ethplorer/wiki/Ethplorer-API#get-token-info
        //        $this->holders = Http::get('https://api.ethplorer.io/getTokenInfo/0xb2e96a63479c2edd2fd62b382c89d5ca79f572d3', [
        //            'apiKey' => 'freekey',
        //        ])->json();

    }
}
