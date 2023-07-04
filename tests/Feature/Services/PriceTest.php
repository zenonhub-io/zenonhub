<?php

namespace Tests\Feature\Services;

use App;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PriceTest extends TestCase
{
    use RefreshDatabase;

    public function test_current_price_can_be_loaded()
    {
        $price = App::make('coingeko.api')->currentPrice();
        $this->assertIsFloat($price);
    }

    public function test_historic_usd_price_can_be_loaded()
    {
        $date = \Carbon\Carbon::parse('01-07-2023');
        $znnPrice = App::make('coingeko.api')->historicPrice('zenon-2', 'usd', $date->timestamp);
        $this->assertIsFloat($znnPrice);
    }
}
