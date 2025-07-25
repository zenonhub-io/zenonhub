<?php

declare(strict_types=1);

namespace App\Actions\Sync;

use App\Exceptions\ZenonRpcException;
use App\Models\Nom\Pillar;
use App\Services\ZenonSdk\ZenonSdk;
use Illuminate\Console\Command;
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
        $previousProducedMomentums = $pillar->produced_momentums;
        $previousExpectedMomentums = $pillar->expected_momentums;

        // Check if no new momentums have been produced
        // Check if expected momentums have changed
        if (
            $currentProducedMomentums === $previousProducedMomentums &&
            $currentExpectedMomentums !== $previousExpectedMomentums
        ) {
            $missed = true;
        }

        $pillar->rank = $pillarDTO->rank;
        $pillar->weight = $pillarDTO->weight;
        $pillar->produced_momentums = $pillarDTO->currentStats->producedMomentums;
        $pillar->expected_momentums = $pillarDTO->currentStats->expectedMomentums;

        if ($missed) {
            $pillar->missed_momentums = min($pillar->missed_momentums + 1, 999);
        } elseif ($pillar->produced_momentums > 0) {
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
