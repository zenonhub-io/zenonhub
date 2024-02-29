<?php

namespace Database\Seeders\Nom;

use App\Models\Nom\Chain;
use Illuminate\Database\Seeder;

class ChainsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run() : void
    {
        Chain::create([
            'chain_identifier' => 1,
            'version' => 1,
            'name' => 'Network of Momentum',
            'is_active' => true,
            'created_at' => '2021-11-24 12:00:00',
        ]);
    }
}
