<?php

declare(strict_types=1);

namespace App\Jobs\Sync;

use App\Domains\Nom\Models\Pillar;
use App\Services\ZenonSdk;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

class Pillars implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 5;

    protected Collection $pillars;

    public function handle()
    {
        try {
            $this->loadPillars();
            $this->processPillars();
        } catch (Throwable $exception) {
            Log::warning('Sync pillars error');
            Log::debug($exception);
            $this->release(30);
        }
    }

    private function loadPillars(): void
    {
        $znn = App::make(ZenonSdk::class);
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

            $chain = app('currentChain');
            $ownerAddress = load_account($data->ownerAddress);
            $producerAddress = load_account($data->producerAddress);
            $withdrawAddress = load_account($data->withdrawAddress);

            if (! $pillar) {
                $pillar = Pillar::create([
                    'chain_id' => $chain->id,
                    'owner_id' => $ownerAddress?->id,
                    'producer_account_id' => $producerAddress?->id,
                    'withdraw_account_id' => $withdrawAddress?->id,
                    'name' => $data->name,
                    'slug' => Str::slug($data->name),
                    'weight' => $data->weight,
                    'produced_momentums' => $data->currentStats->producedMomentums,
                    'expected_momentums' => $data->currentStats->expectedMomentums,
                    'momentum_rewards' => $data->giveMomentumRewardPercentage,
                    'delegate_rewards' => $data->giveDelegateRewardPercentage,
                ]);
            }

            // If  no more produced momentums its missed one or more
            $missed = false;
            if (
                $pillar->produced_momentums === $data->currentStats->producedMomentums &&
                $pillar->produced_momentums < $pillar->expected_momentums) {
                $missed = true;
            }

            $pillar->producer_account_id = $producerAddress?->id;
            $pillar->withdraw_account_id = $withdrawAddress?->id;
            $pillar->weight = $data->weight;
            $pillar->produced_momentums = $data->currentStats->producedMomentums;
            $pillar->expected_momentums = $data->currentStats->expectedMomentums;

            if ($missed) {
                if ($pillar->missed_momentums < 999) {
                    $pillar->missed_momentums++;
                }
            } elseif ($pillar->expected_momentums > 0) {
                $pillar->missed_momentums = 0;
            }

            $pillar->save();
        });
    }
}
