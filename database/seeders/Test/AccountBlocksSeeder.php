<?php

declare(strict_types=1);

namespace Database\Seeders\Test;

use App\DataTransferObjects\Nom\AccountBlockDTO;
use App\Models\Nom\AccountBlock;
use App\Models\Nom\AccountBlockData;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
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

        $accountBlocksJson = Storage::json('json/test/transactions.json');
        $accountBlocksDTO = AccountBlockDTO::collect($accountBlocksJson, Collection::class);

        $accountBlocksDTO->each(function ($accountBlockDTO) {
            AccountBlock::insert([
                'chain_id' => $accountBlockDTO->chainIdentifier,
                'account_id' => load_account($accountBlockDTO->address)->id,
                'to_account_id' => load_account($accountBlockDTO->toAddress)->id,
                'momentum_id' => 1,
                'version' => $accountBlockDTO->version,
                'block_type' => $accountBlockDTO->blockType,
                'height' => $accountBlockDTO->height,
                'nonce' => $accountBlockDTO->nonce,
                'hash' => $accountBlockDTO->hash,
                'created_at' => $accountBlockDTO->confirmationDetail->momentumTimestamp,
            ]);
        });
    }
}
