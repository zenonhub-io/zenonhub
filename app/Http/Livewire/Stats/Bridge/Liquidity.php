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

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
        ])->post('https://api.thegraph.com/subgraphs/name/ianlapham/uniswap-v2-dev', [
            'query' => $query,
        ])->json('data.pair');

        dd($response);

        // reserve0 = pooled wZNN
        // reserve1 = pooled wETH
        // reserveUSD = usd liquidity
        // token0Price = eth = x znn
        // token1Price = znn = x eth

        // total supply & holders
        // https://github.com/EverexIO/Ethplorer/wiki/Ethplorer-API#get-token-info
        $response = Http::get('https://api.ethplorer.io/getTokenInfo/0xb2e96a63479c2edd2fd62b382c89d5ca79f572d3', [
            'apiKey' => 'freekey',
        ])->json();

        dd($response);

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
