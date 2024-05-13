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
        $pillars = Storage::json('nom-json/genesis/genesis.json')['PillarConfig']['Pillars'];

        collect($pillars)->each(function ($pillar) use ($chainId) {
            $pillar = Pillar::create([
                'chain_id' => $chainId,
                'owner_id' => load_account($pillar['StakeAddress'])->id,
                'producer_account_id' => load_account($pillar['BlockProducingAddress'])->id,
                'withdraw_account_id' => load_account($pillar['RewardWithdrawAddress'])->id,
                'name' => $pillar['Name'],
                'slug' => Str::slug($pillar['Name']),
                'qsr_burn' => $pillar['Amount'],
                'momentum_rewards' => $pillar['GiveBlockRewardPercentage'],
                'delegate_rewards' => $pillar['GiveDelegateRewardPercentage'],
                'is_legacy' => 1,
                'created_at' => '2021-11-24 12:00:00',
            ]);

            $pillar->history()->create([
                'producer_account_id' => load_account($pillar['BlockProducingAddress'])->id,
                'withdraw_account_id' => load_account($pillar['RewardWithdrawAddress'])->id,
                'momentum_rewards' => $pillar['GiveBlockRewardPercentage'],
                'delegate_rewards' => $pillar['GiveDelegateRewardPercentage'],
                'is_reward_change' => false,
                'updated_at' => '2021-11-24 12:00:00',
            ]);
        });
    }
}
