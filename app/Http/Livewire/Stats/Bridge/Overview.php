<?php

namespace App\Http\Livewire\Stats\Bridge;

use App\Models\Nom\Account;
use DigitalSloth\ZnnPhp\Zenon;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Http;
use Livewire\Component;

class Overview extends Component
{
    private Zenon $znn;

    public ?array $liquidityData;

    public ?array $holders;

    public function __construct($id = null)
    {
        parent::__construct($id);
        $this->znn = App::make('zenon.api');
    }

    public function render()
    {
        $bridgeStats = $this->znn->bridge->getBridgeInfo()['data'];

        return view('livewire.stats.bridge.overview', [
            'adminAddress' => Account::findByAddress($bridgeStats->administrator),
            'halted' => $bridgeStats->halted,
        ]);
    }

    public function loadLiquidityData()
    {

        // Orbital Staked ETH = (Orbital’s Balance of ETH-wZNN LP ZTS) / (Total Supply of ETH-wZNN LP ERC20) * (Amount of ETH in the ETH-wZNN Pool)

        // wZNN token: 0xb2e96a63479c2edd2fd62b382c89d5ca79f572d3
        // pair: 0xdac866a3796f85cb84a914d98faec052e3b5596d

        // Total liquidity
        // 24h volume
        // Total Supply
        // Holders

        // https://api.thegraph.com/subgraphs/name/ianlapham/uniswap-v2-dev

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
  reserveUSD
  reserve0
  reserve1
  volumeUSD
  volumeToken0
  volumeToken1
  token0Price
  token1Price
  txCount
 }
}
GQL;

        // reserve0 = pooled wZNN
        // reserve1 = pooled wETH
        // reserveUSD = usd liquidity
        // token0Price = eth = x znn
        // token1Price = znn = x eth

        $this->liquidityData = Http::withHeaders([
            'Content-Type' => 'application/json',
        ])->post('https://api.thegraph.com/subgraphs/name/ianlapham/uniswap-v2-dev', [
            'query' => $query,
        ])->json('data.pair');

        // total supply & holders
        // https://github.com/EverexIO/Ethplorer/wiki/Ethplorer-API#get-token-info
        $this->holders = Http::get('https://api.ethplorer.io/getTokenInfo/0xb2e96a63479c2edd2fd62b382c89d5ca79f572d3', [
            'apiKey' => 'freekey',
        ])->json();

    }
}
