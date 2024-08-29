<?php

declare(strict_types=1);

namespace Database\Seeders\Nom\Test;

use App\DataTransferObjects\Nom\PillarDTO;
use App\Models\Nom\Pillar;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
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
        $pillarsJson = Storage::json('nom-json/test/pillars.json');
        $pillarsDTO = PillarDTO::collect($pillarsJson, Collection::class);

        $pillarsDTO->each(function ($pillarDTO) use ($chain) {

            $owner = load_account($pillarDTO->ownerAddress);
            $producer = load_account($pillarDTO->producerAddress);
            $withdraw = load_account($pillarDTO->withdrawAddress);

            $pillar = Pillar::create([
                'chain_id' => $chain->id,
                'owner_id' => $owner->id,
                'producer_account_id' => $producer->id,
                'withdraw_account_id' => $withdraw->id,
                'name' => $pillarDTO->name,
                'slug' => Str::slug($pillarDTO->name),
                'qsr_burn' => 150000 * NOM_DECIMALS,
                'weight' => 0,
                'produced_momentums' => 0,
                'expected_momentums' => 0,
                'missed_momentums' => 0,
                'momentum_rewards' => $pillarDTO->giveMomentumRewardPercentage,
                'delegate_rewards' => $pillarDTO->giveDelegateRewardPercentage,
                'az_engagement' => '0.00',
                'az_avg_vote_time' => null,
                'avg_momentums_produced' => 0,
                'total_momentums_produced' => 0,
                'is_legacy' => 1,
                'revoked_at' => null,
                'created_at' => '2021-11-24 12:00:00',
                'updated_at' => null,
            ]);

            // Create history with different producer address
            if ($pillar->id === 1) {
                $producer = load_account('z1qxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxprod0');
            }

            $pillar->updates()->create([
                'producer_account_id' => $producer->id,
                'withdraw_account_id' => $withdraw->id,
                'momentum_rewards' => $pillarDTO->giveMomentumRewardPercentage - 1,
                'delegate_rewards' => $pillarDTO->giveDelegateRewardPercentage - 1,
                'is_reward_change' => 1,
                'updated_at' => '2021-11-24 12:00:00',
            ]);
        });
    }
}
