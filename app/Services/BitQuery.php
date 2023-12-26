<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class BitQuery
{
    protected string $baseUrl = 'https://graphql.bitquery.io';

    public function __construct(protected string $apiKey)
    {
    }

    public function getLiquidityData()
    {
        // $liquidity;
        // $pooledEth;
        // $pooledWznn;
        // $DailyVolume;

        $weekAgo = now()->subWeek()->toISOString();
        $poolAddress = config('zenon.bridge.ethereum.znn-eth-pool');
        $request = <<<GQL
{
	ethereum(network: ethereum) {
		dexTrades(
			options: { desc: "date.date" }
			time: { since: "{$weekAgo}" }
			smartContractAddress: { is: "{$poolAddress}" }
		) {
			date {
				date(format: "%y-%m-%d")
			}
			tradeAmount(in:USD)
		}
        address( address: { is: "{$poolAddress}" }) {
        	balances {
        		currency {
        			symbol
        		}
        		value
        	}
        }
    }
}
GQL;

        $rememberFor = now()->addHours(4);

        return Cache::remember('bitquery-ethznn-pool-data', $rememberFor, function () use ($request) {
            return $this->makeRequest($request, 'data.ethereum');
        });
    }

    private function getRequestHeaders(): array
    {
        return [
            'X-API-KEY' => $this->apiKey,
        ];
    }

    private function getRequestBody(string $query): array
    {
        return [
            'query' => $query,
        ];
    }

    private function makeRequest(string $query, ?string $jsonValue = null)
    {
        return Http::withHeaders($this->getRequestHeaders())
            ->post($this->baseUrl, $this->getRequestBody($query))
            ->json($jsonValue);
    }
}
