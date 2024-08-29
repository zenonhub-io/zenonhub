<?php

declare(strict_types=1);

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CoinGecko
{
    public function currentPrice(string $token = 'zenon-2', string $currency = 'usd'): ?float
    {
        $response = Http::get("https://api.coingecko.com/api/v3/simple/price?ids={$token}&vs_currencies={$currency}");

        if ($response->successful()) {
            return $response->json("{$token}.{$currency}");
        }

        Log::warning('Unable to load price from coingeko');

        return false;
    }

    public function historicPrice(string $token, string $currency, Carbon $timestamp): ?float
    {
        $date = $timestamp->format('d-m-Y');

        return Cache::rememberForever("coingecko-price-{$token}-{$currency}-{$date}", function () use ($token, $date, $currency) {
            return Http::get("https://api.coingecko.com/api/v3/coins/{$token}/history?date={$date}")
                ->json("market_data.current_price.{$currency}");
        });
    }
}
