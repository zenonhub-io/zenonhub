<?php

declare(strict_types=1);

namespace Database\Seeders\Site;

use App\Models\Nom\Chain;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class ChainsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        Chain::truncate();
        Schema::enableForeignKeyConstraints();

        Chain::create([
            'chain_identifier' => 1,
            'version' => 1,
            'name' => 'Network of Momentum',
            'code' => 'nom',
            'is_active' => true,
            'created_at' => '2021-11-24 12:00:00',
        ]);

        Chain::create([
            'chain_identifier' => 26,
            'version' => 1,
            'name' => 'HyperQube',
            'code' => 'hqz',
            'is_active' => true,
            'created_at' => '2025-02-19 00:00:00',
        ]);
    }
}
