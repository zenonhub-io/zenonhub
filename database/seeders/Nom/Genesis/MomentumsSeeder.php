<?php

declare(strict_types=1);

namespace Database\Seeders\Nom\Genesis;

use App\Domains\Nom\Models\Momentum;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class MomentumsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        Momentum::truncate();
        Schema::enableForeignKeyConstraints();

        $chainId = app('currentChain')->id;
        $momentums = [
            [
                'producer' => 'z1qznll3hchu0dwej3c9r4dgrp6e30tq8l7qv2em',
                'hash' => '9e204601d1b7b1427fe12bc82622e610d8a6ad43c40abf020eb66e538bb8eeb0',
                'data' => 'MDAwMDAwMDAwMDAwMDAwMDAwMDA0ZGQwNDA1OTU1NDBkNDNjZThmZjU5NDZlZWFhNDAzZmIxM2QwZTU4MmQ4ZiNXZSBhcmUgYWxsIFNhdG9zaGkjRG9uJ3QgdHJ1c3QuIFZlcmlmeQ==',
                'height' => 1,
            ],
        ];

        foreach ($momentums as $momentumData) {
            Momentum::insert([
                'chain_id' => $chainId,
                'producer_account_id' => load_account($momentumData['producer'])->id,
                'height' => $momentumData['height'],
                'hash' => $momentumData['hash'],
                'data' => $momentumData['data'],
                'created_at' => '2021-11-24 12:00:00',
            ]);
        }
    }
}
