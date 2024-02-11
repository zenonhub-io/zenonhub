<?php

namespace Database\Seeders\GenesisMomentum;

use Illuminate\Database\Seeder;

class NomTokensTableSeeder extends Seeder
{
    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        \DB::table('nom_tokens')->truncate();

        \DB::table('nom_tokens')->insert([
            0 => [
                'id' => 1,
                'chain_id' => 1,
                'owner_id' => 4,
                'name' => 'ZNN',
                'symbol' => 'ZNN',
                'domain' => 'zenon.network',
                'token_standard' => 'zts1znnxxxxxxxxxxxxx9z4ulx',
                'total_supply' => 0,
                'max_supply' => 9007199254740991,
                'decimals' => 8,
                'is_burnable' => 1,
                'is_mintable' => 1,
                'is_utility' => 1,
                'created_at' => '2021-11-24 12:00:00',
                'updated_at' => null,
            ],
            1 => [
                'id' => 2,
                'chain_id' => 1,
                'owner_id' => 4,
                'name' => 'QSR',
                'symbol' => 'QSR',
                'domain' => 'zenon.network',
                'token_standard' => 'zts1qsrxxxxxxxxxxxxxmrhjll',
                'total_supply' => 0,
                'max_supply' => 9007199254740991,
                'decimals' => 8,
                'is_burnable' => 1,
                'is_mintable' => 1,
                'is_utility' => 1,
                'created_at' => '2021-11-24 12:00:00',
                'updated_at' => null,
            ],
        ]);
    }
}
