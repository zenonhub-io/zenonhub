<?php

declare(strict_types=1);

namespace Database\Seeders\HyperQube\Genesis;

use App\Models\Nom\Momentum;
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

        $chain = app('currentChain');

        Momentum::insert([
            'chain_id' => $chain->id,
            'producer_account_id' => load_account('z1qznll3hchu0dwej3c9r4dgrp6e30tq8l7qv2em')->id,
            'height' => 1,
            'hash' => 'e8a65a65b873482e34b82fa8eebf2f25936b677f6b7514735f56a8379b1d86f0',
            'data' => 'SFlQRVJRVUJFIFogVU5JRk9STSA2MA==',
            'created_at' => $chain->created_at->format('Y-m-d H:i:s'),
        ]);
    }
}
