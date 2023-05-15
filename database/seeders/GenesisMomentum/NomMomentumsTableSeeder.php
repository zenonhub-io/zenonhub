<?php

namespace Database\Seeders\GenesisMomentum;

use Illuminate\Database\Seeder;

class NomMomentumsTableSeeder extends Seeder
{
    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        \DB::table('nom_momentums')->truncate();

        \DB::table('nom_momentums')->insert([
            0 => [
                'id' => 1,
                'chain_id' => 1,
                'producer_account_id' => 78,
                'producer_pillar_id' => null,
                'version' => 1,
                'height' => 1,
                'hash' => '9e204601d1b7b1427fe12bc82622e610d8a6ad43c40abf020eb66e538bb8eeb0',
                'data' => 'MDAwMDAwMDAwMDAwMDAwMDAwMDA0ZGQwNDA1OTU1NDBkNDNjZThmZjU5NDZlZWFhNDAzZmIxM2QwZTU4MmQ4ZiNXZSBhcmUgYWxsIFNhdG9zaGkjRG9uJ3QgdHJ1c3QuIFZlcmlmeQ==',
                'created_at' => '2021-11-24 12:00:00',
            ],
        ]);
    }
}
