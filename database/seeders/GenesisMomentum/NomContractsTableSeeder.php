<?php

namespace Database\Seeders\GenesisMomentum;

use Illuminate\Database\Seeder;

class NomContractsTableSeeder extends Seeder
{
    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        \DB::table('nom_contracts')->delete();

        \DB::table('nom_contracts')->insert([
            0 => [
                'id' => 1,
                'chain_id' => 1,
                'account_id' => 9,
                'name' => 'Accelerator',
            ],
            1 => [
                'id' => 2,
                'chain_id' => 1,
                'account_id' => 11,
                'name' => 'Bridge',
            ],
            2 => [
                'id' => 3,
                'chain_id' => 1,
                'account_id' => null,
                'name' => 'Common',
            ],
            3 => [
                'id' => 4,
                'chain_id' => 1,
                'account_id' => 12,
                'name' => 'HTLC',
            ],
            4 => [
                'id' => 5,
                'chain_id' => 1,
                'account_id' => 10,
                'name' => 'Liquidity',
            ],
            5 => [
                'id' => 6,
                'chain_id' => 1,
                'account_id' => 3,
                'name' => 'Pillar',
            ],
            6 => [
                'id' => 7,
                'chain_id' => 1,
                'account_id' => 2,
                'name' => 'Plasma',
            ],
            7 => [
                'id' => 8,
                'chain_id' => 1,
                'account_id' => 5,
                'name' => 'Sentinel',
            ],
            8 => [
                'id' => 9,
                'chain_id' => 1,
                'account_id' => 7,
                'name' => 'Stake',
            ],
            9 => [
                'id' => 10,
                'chain_id' => 1,
                'account_id' => 6,
                'name' => 'Swap',
            ],
            10 => [
                'id' => 11,
                'chain_id' => 1,
                'account_id' => 4,
                'name' => 'Token',
            ],
        ]);
    }
}
