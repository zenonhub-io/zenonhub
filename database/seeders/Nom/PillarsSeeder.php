<?php

declare(strict_types=1);

namespace Database\Seeders\Nom;

use App\Domains\Nom\Models\Pillar;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PillarsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $chainId = app('currentChain')->id;
        $pillars = Storage::json('nom-json/genesis/pillars.json');

        foreach ($pillars as $pillar) {
            Pillar::insert([
                'chain_id' => $chainId,
                'owner_id' => load_account($pillar['owner'])->id,
                'producer_account_id' => load_account($pillar['producer'])->id,
                'withdraw_account_id' => load_account($pillar['withdraw'])->id,
                'name' => $pillar['name'],
                'slug' => Str::slug($pillar['name']),
                'momentum_rewards' => $pillar['momentum_rewards'],
                'delegate_rewards' => $pillar['delegate_rewards'],
                'is_legacy' => 1,
                'created_at' => '2021-11-24 12:00:00',
            ]);
        }
    }
}
