<?php

declare(strict_types=1);

namespace App\Actions\Sync;

use App\Exceptions\ZenonRpcException;
use App\Models\Nom\Pillar;
use App\Services\ZenonSdk\ZenonSdk;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Lorisleiva\Actions\Concerns\AsAction;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\ConsoleOutput;

class PillarMetrics
{
    use AsAction;

    public string $commandSignature = 'sync:pillar-metrics';

    public function handle(Pillar $pillar): void
    {
        try {
            $pillarDTO = app(ZenonSdk::class)->getPillarByName($pillar->name);
        } catch (ZenonRpcException $e) {
            return;
        }

        $missed = false;
        $currentProducedMomentums = $pillarDTO->currentStats->producedMomentums;
        $currentExpectedMomentums = $pillarDTO->currentStats->expectedMomentums;

        // Produced momentums has not changed
        // Ensure there are expected momentums
        // Current produced is less than expected
        if (
            $currentProducedMomentums === $pillar->produced_momentums &&
            $currentExpectedMomentums > $currentProducedMomentums &&
            $currentExpectedMomentums > 0
        ) {
            $missed = true;
        }

        Log::debug('Pillar metrics', [
            'missed' => $missed,
            'produced' => $pillar->produced_momentums,
            'expected' => $pillar->expected_momentums,
        ]);

        $pillar->rank = $pillarDTO->rank;
        $pillar->weight = $pillarDTO->weight;
        $pillar->produced_momentums = $currentProducedMomentums;
        $pillar->expected_momentums = $currentExpectedMomentums;

        if ($missed) {
            // Increment missed momentums, ensuring it doesn't exceed 999
            $pillar->missed_momentums = min($pillar->missed_momentums + 1, 999);
        } elseif ($pillar->produced_momentums > 0) {
            // If it has produced momentums reset missed momentums to 0
            $pillar->missed_momentums = 0;
        }

        $pillar->save();
    }

    public function asCommand(Command $command): void
    {
        $pillars = Pillar::whereActive()->get();

        $progressBar = new ProgressBar(new ConsoleOutput, $pillars->count());
        $progressBar->start();

        $pillars->each(function (Pillar $pillar) use ($progressBar): void {
            $this->handle($pillar);
            $progressBar->advance();
        });

        $progressBar->finish();
    }
}
