<?php

declare(strict_types=1);

namespace Database\Seeders\Nom\Genesis;

use App\Models\Nom\Pillar;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PillarsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        Pillar::truncate();
        Schema::enableForeignKeyConstraints();

        $chainId = app('currentChain')->id;
        $pillars = Storage::json('nom-json/genesis/genesis.json')['PillarConfig']['Pillars'];

        collect($pillars)->each(function ($pillarData) use ($chainId) {
            $pillar = Pillar::create([
                'chain_id' => $chainId,
                'owner_id' => load_account($pillarData['StakeAddress'])->id,
                'producer_account_id' => load_account($pillarData['BlockProducingAddress'])->id,
                'withdraw_account_id' => load_account($pillarData['RewardWithdrawAddress'])->id,
                'name' => $pillarData['Name'],
                'slug' => Str::slug($pillarData['Name']),
                'qsr_burn' => 150000 * config('nom.decimals'),
                'momentum_rewards' => $pillarData['GiveBlockRewardPercentage'],
                'delegate_rewards' => $pillarData['GiveDelegateRewardPercentage'],
                'is_legacy' => 1,
                'created_at' => '2021-11-24 12:00:00',
            ]);

            $pillar->updateHistory()->create([
                'producer_account_id' => load_account($pillarData['BlockProducingAddress'])->id,
                'withdraw_account_id' => load_account($pillarData['RewardWithdrawAddress'])->id,
                'momentum_rewards' => $pillarData['GiveBlockRewardPercentage'],
                'delegate_rewards' => $pillarData['GiveDelegateRewardPercentage'],
                'is_reward_change' => false,
                'updated_at' => '2021-11-24 12:00:00',
            ]);
        });
    }
}
