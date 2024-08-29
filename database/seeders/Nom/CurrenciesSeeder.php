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
            'symbol' => 'BTC',
        ]);

        Currency::create([
            'name' => 'Ethereum',
            'symbol' => 'ETH',
        ]);

        Currency::create([
            'name' => 'Dollar (US)',
            'symbol' => 'USD',
            'icon' => '$',
        ]);

        Currency::create([
            'name' => 'Pound',
            'symbol' => 'GBP',
            'icon' => '£',
        ]);

        Currency::create([
            'name' => 'Euro',
            'symbol' => 'EUR',
            'icon' => '€',
        ]);
    }
}
