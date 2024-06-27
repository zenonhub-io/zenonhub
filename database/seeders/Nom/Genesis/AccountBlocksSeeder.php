<?php

declare(strict_types=1);

namespace Database\Seeders\Nom\Genesis;

use App\Domains\Nom\Models\AccountBlock;
use App\Domains\Nom\Models\AccountBlockData;
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

        $chainId = app('currentChain')->id;
        $accountBlocks = Storage::json('nom-json/genesis/account-blocks.json');

        collect($accountBlocks)->each(function ($accountBlock) use ($chainId) {
            AccountBlock::insert([
                'chain_id' => $chainId,
                'account_id' => load_account($accountBlock['account'])->id,
                'to_account_id' => load_account($accountBlock['to_account'])->id,
                'momentum_id' => 1,
                'block_type' => 1,
                'height' => 1,
                'nonce' => '0000000000000000',
                'hash' => $accountBlock['hash'],
                'created_at' => '2021-11-24 12:00:00',
            ]);
        });
    }
}
