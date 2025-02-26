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

        $chain = app('currentChain');
        $pillars = Storage::json('json/nom/genesis.json')['PillarConfig']['Pillars'];

        collect($pillars)->each(function ($pillarData) use ($chain) {
            $pillar = Pillar::create([
                'chain_id' => $chain->id,
                'owner_id' => load_account($pillarData['StakeAddress'])->id,
                'producer_account_id' => load_account($pillarData['BlockProducingAddress'])->id,
                'withdraw_account_id' => load_account($pillarData['RewardWithdrawAddress'])->id,
                'name' => $pillarData['Name'],
                'slug' => Str::slug($pillarData['Name']),
                'qsr_burn' => 150000 * config('nom.decimals'),
                'momentum_rewards' => $pillarData['GiveBlockRewardPercentage'],
                'delegate_rewards' => $pillarData['GiveDelegateRewardPercentage'],
                'is_legacy' => 1,
                'created_at' => $chain->created_at->format('Y-m-d H:i:s'),
            ]);

            $pillar->updateHistory()->create([
                'producer_account_id' => load_account($pillarData['BlockProducingAddress'])->id,
                'withdraw_account_id' => load_account($pillarData['RewardWithdrawAddress'])->id,
                'momentum_rewards' => $pillarData['GiveBlockRewardPercentage'],
                'delegate_rewards' => $pillarData['GiveDelegateRewardPercentage'],
                'is_reward_change' => false,
                'updated_at' => $chain->created_at->format('Y-m-d H:i:s'),
            ]);
        });
    }
}
