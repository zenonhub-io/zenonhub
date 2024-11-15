<?php

declare(strict_types=1);

namespace Database\Seeders\Nom;

use App\Models\Nom\Currency;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class CurrenciesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        Currency::truncate();
        Schema::enableForeignKeyConstraints();

        Currency::create([
            'name' => 'Bitcoin',
            'code' => 'btc',
            'symbol' => 'BTC',
            'icon' => '₿',
        ]);

        Currency::create([
            'name' => 'Ethereum',
            'code' => 'eth',
            'symbol' => 'ETH',
            'icon' => 'Ξ',
        ]);

        Currency::create([
            'name' => 'Dollar (US)',
            'code' => 'usd',
            'symbol' => 'USD',
            'icon' => '$',
            'id_default' => true,
        ]);

        Currency::create([
            'name' => 'Pound',
            'code' => 'gbp',
            'symbol' => 'GBP',
            'icon' => '£',
        ]);

        Currency::create([
            'name' => 'Euro',
            'code' => 'eur',
            'symbol' => 'EUR',
            'icon' => '€',
        ]);
    }
}
