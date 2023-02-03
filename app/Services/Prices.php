<?php

namespace App\Services;

use Http;
use Log;
use Carbon\Carbon;

class Prices
{
    public function currenPrice(string $token = 'zenon', string $currency = 'usd'): ?float
    {
        $response = Http::get("https://api.coingecko.com/api/v3/simple/price?ids={$token}&vs_currencies={$currency}");

        if ($response->successful()) {
            return $response->json("{$token}.{$currency}");
        }

        Log::warning('Unable to load price from coingeko');
        return false;
    }

    public function historicPrice(string $token, string $currency, int $timestamp): ?float
    {
        $date = Carbon::parse($timestamp)->format('d-m-Y');
        $response = Http::get("https://api.coingecko.com/api/v3/coins/{$token}/history?date={$date}");

        if ($response->successful()) {
            return $response->json("market_data.current_price.usd{$currency}");
        }

        Log::warning('Unable to load historic price from coingeko');
        return false;
    }
}
