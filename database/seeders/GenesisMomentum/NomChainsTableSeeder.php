<?php

declare(strict_types=1);

namespace Database\Seeders\GenesisMomentum;

use DB;
use Illuminate\Database\Seeder;

class NomChainsTableSeeder extends Seeder
{
    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        DB::table('nom_chains')->truncate();

        DB::table('nom_chains')->insert([
            0 => [
                'id' => 1,
                'chain_identifier' => 1,
                'version' => 1,
                'name' => 'Network of Momentum',
                'is_active' => 1,
                'created_at' => '2021-11-24 12:00:00',
            ],
        ]);
    }
}
