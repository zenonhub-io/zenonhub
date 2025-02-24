<?php

declare(strict_types=1);

namespace Database\Seeders\Nom\Genesis;

use App\Models\Nom\AccountBlock;
use App\Models\Nom\AccountBlockData;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class AccountBlocksSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        AccountBlock::truncate();
        AccountBlockData::truncate();
        Schema::enableForeignKeyConstraints();

        $chain = app('currentChain');
        $accountBlocks = Storage::json('json/nom/genesis-account-blocks.json');

        collect($accountBlocks)->each(function ($accountBlock) use ($chain) {
            AccountBlock::insert([
                'chain_id' => $chain->id,
                'account_id' => load_account($accountBlock['account'])->id,
                'to_account_id' => load_account($accountBlock['to_account'])->id,
                'momentum_id' => 1,
                'block_type' => 1,
                'height' => 1,
                'nonce' => '0000000000000000',
                'hash' => $accountBlock['hash'],
                'created_at' => $chain->created_at->format('Y-m-d H:i:s'),
            ]);
        });
    }
}
