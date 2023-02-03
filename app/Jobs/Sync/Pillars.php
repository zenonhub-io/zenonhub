<?php

namespace App\Jobs\Sync;

use App;
use Log;
use Str;
use Throwable;
use App\Classes\Utilities;
use App\Models\Nom\Pillar;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class Pillars implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 25;
    public int $backoff = 10;
    protected Collection $pillars;

    public function __construct()
    {
        $this->onQueue('indexer');
    }

    public function handle()
    {
        try {
            $this->loadPillars();
            $this->processPillars();
            $this->processMissingPillars();
        } catch (\DigitalSloth\ZnnPhp\Exceptions\Exception) {
            Log::error('Sync pillars error - could not load data from API');
            $this->release(10);
        } catch (Throwable $exception) {
            Log::error('Sync pillars error - ' . $exception);
            $this->release(10);
        }
    }

    private function loadPillars(): void
    {
        $znn = App::make('zenon.api');
        $total = null;
        $results = [];
        $page = 0;

        while (count($results) !== $total) {
            $data = $znn->pillar->getAll($page);
            if ($data['status']) {
                if (is_null($total)) {
                    $total = $data['data']->count;
                }
                $results = array_merge($results, $data['data']->list);
            }

            $page++;
        }

        $this->pillars = collect($results);
    }

    private function processPillars()
    {
        $this->pillars->each(function ($data) {
            $pillar = Pillar::where('name', $data->name)->first();

            $ownerAddress = Utilities::loadAccount($data->ownerAddress);
            $producerAddress = Utilities::loadAccount($data->producerAddress);
            $withdrawAddress = Utilities::loadAccount($data->withdrawAddress);

            if (! $pillar) {
                $pillar = Pillar::create([
                    'owner_id' => $ownerAddress?->id,
                    'producer_id' => $producerAddress?->id,
                    'withdraw_id' => $withdrawAddress?->id,
                    'name' => $data->name,
                    'slug' => Str::slug($data->name),
                    'weight' => $data->weight,
                    'produced_momentums' => $data->currentStats->producedMomentums,
                    'expected_momentums' => $data->currentStats->expectedMomentums,
                    'give_momentum_reward_percentage' => $data->giveMomentumRewardPercentage,
                    'give_delegate_reward_percentage' => $data->giveDelegateRewardPercentage,
                ]);
            }

            // If  no more produced momentums its missed one or more
            $missed = false;
            if (
                $pillar->produced_momentums === $data->currentStats->producedMomentums &&
                $pillar->produced_momentums < $pillar->expected_momentums) {
                $missed = true;
            }

            $pillar->producer_id = $producerAddress?->id;
            $pillar->withdraw_id = $withdrawAddress?->id;
            $pillar->weight = $data->weight;
            $pillar->produced_momentums = $data->currentStats->producedMomentums;
            $pillar->expected_momentums = $data->currentStats->expectedMomentums;

            if ($missed) {
                if($pillar->missed_momentums < 999) {
                    $pillar->missed_momentums++;
                }
            } else if($pillar->expected_momentums > 0) {
                $pillar->missed_momentums = 0;
            }

            $pillar->save();
        });
    }

    private function processMissingPillars()
    {
        $missingPillars = config('zenon.missing_pillars');
        foreach ($missingPillars as $name => $account) {
            $pillar = Pillar::where('name', $name)->first();
            if (! $pillar) {
                $account = Utilities::loadAccount($account);
                Pillar::create([
                    'owner_id' => $account?->id,
                    'producer_id' => $account?->id,
                    'withdraw_id' => $account?->id,
                    'name' => $name,
                    'slug' => Str::slug($name),
                ]);
            }
        }
    }
}
