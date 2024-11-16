<?php

declare(strict_types=1);

namespace App\Actions\Sync;

use App\Models\Nom\AcceleratorPhase;
use App\Models\Nom\AcceleratorProject;
use App\Models\Nom\Pillar;
use Illuminate\Console\Command;
use Lorisleiva\Actions\Concerns\AsAction;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\ConsoleOutput;

class PillarEngagementScore
{
    use AsAction;

    public string $commandSignature = 'sync:pillar-engagement-scores';

    public function handle(Pillar $pillar): void
    {
        $totalProjects = AcceleratorProject::where('created_at', '>=', $pillar->created_at)->count();
        $totalPhases = AcceleratorPhase::where('created_at', '>=', $pillar->created_at)->count();
        $totalVotableItems = ($totalProjects + $totalPhases);

        if (! $totalVotableItems) {
            return;
        }

        $pillar->az_engagement = 0;

        // Make sure the vote item was created after the pillar
        // Projects/phases might be open after a pillar spawned, dont include these
        $votes = $pillar->votes()->whereHasMorph('votable', [
            AcceleratorProject::class,
            AcceleratorPhase::class,
        ])->with('votable')->get();
        $totalVotes = $votes->map(function ($vote) use ($pillar) {
            if ($vote->votable->created_at >= $pillar->created_at) {
                return 1;
            }

            return 0;
        })->sum();

        // If a pillar has more votes than projects ensure the pillar doenst get over 100% engagement
        if ($totalVotes > $totalVotableItems) {
            $totalVotes = $totalVotableItems;
        }

        $percentage = ($totalVotes * 100) / $totalVotableItems;
        $pillar->az_engagement = round($percentage, 1);

        $averageVoteTime = $pillar
            ->votes()
            ->whereHasMorph('votable', [
                AcceleratorProject::class,
                AcceleratorPhase::class,
            ])
            ->with('votable')
            ->get()
            ->map(fn ($vote) => $vote->created_at->timestamp - $vote->votable->created_at->timestamp)
            ->average();

        $pillar->az_avg_vote_time = $averageVoteTime;
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
