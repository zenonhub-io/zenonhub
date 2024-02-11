<?php

namespace Tests\Feature\Services;

use App\Services\CoinGecko;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\App;
use Tests\TestCase;

class PriceTest extends TestCase
{
    use RefreshDatabase;

    public function test_current_price_can_be_loaded()
    {
        $price = App::make(CoinGecko::class)->currentPrice();
        $this->assertIsFloat($price);
    }

    public function test_historic_usd_price_can_be_loaded()
    {
        $date = \Carbon\Carbon::parse('01-07-2023');
        $znnPrice = App::make(CoinGecko::class)->historicPrice('zenon-2', 'usd', $date->timestamp);
        $this->assertIsFloat($znnPrice);
    }
}
