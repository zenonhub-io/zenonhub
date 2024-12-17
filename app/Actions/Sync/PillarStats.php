<?php

declare(strict_types=1);

namespace App\Actions\Sync;

use App\Models\Nom\Pillar;
use Illuminate\Console\Command;
use Lorisleiva\Actions\Concerns\AsAction;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\ConsoleOutput;

class PillarStats
{
    use AsAction;

    public string $commandSignature = 'sync:pillar-stats';

    public function handle(Pillar $pillar): void
    {
        $pillar->statHistory()->updateOrCreate([
            'date' => now()->format('Y-m-d'),
        ], [
            'rank' => $pillar->rank,
            'weight' => $pillar->weight,
            'momentum_rewards' => $pillar->momentum_rewards,
            'delegate_rewards' => $pillar->delegate_rewards,
            'total_delegators' => $pillar->activeDelegators()->count(),
        ]);
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
