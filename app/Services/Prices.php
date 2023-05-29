<?php

namespace App\Services;

use Carbon\Carbon;
use Http;
use Log;

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
        $date = Carbon::createFromTimestamp($timestamp)->format('d-m-Y');

        try {
            $response = Http::get("https://api.coingecko.com/api/v3/coins/{$token}/history?date={$date}");
            if ($response->successful()) {
                return $response->json("market_data.current_price.{$currency}");
            }
        } catch (\Illuminate\Http\Client\ConnectionException) {
            return false;
        }

        Log::warning('Unable to load historic price from coingeko');

        return false;
    }
}
