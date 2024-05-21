<?php

declare(strict_types=1);

namespace App\Domains\Common\Actions;

use App\Domains\Nom\Models\AcceleratorPhase;
use App\Domains\Nom\Models\AcceleratorProject;
use App\Domains\Nom\Models\Pillar;
use Lorisleiva\Actions\Concerns\AsAction;

class UpdatePillarEngagementScore
{
    use AsAction;

    public function handle(Pillar $pillar): void
    {
        $totalProjects = AcceleratorProject::where('created_at', '>=', $pillar->created_at)->count();
        $totalPhases = AcceleratorPhase::where('created_at', '>=', $pillar->created_at)->count();
        $totalVotableItems = ($totalProjects + $totalPhases);

        $pillar->az_engagement = 0;

        if ($totalVotableItems > 0) {

            // Make sure the vote item was created after the pillar
            // Projects/phases might be open after a pillar spawned, dont include these
            $votes = $pillar->votes()->whereHasMorph('votable', [
                AcceleratorProject::class,
                AcceleratorPhase::class,
            ])->get();
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
        }

        $averageVoteTime = $pillar
            ->votes()
            ->whereHasMorph('votable', [
                AcceleratorProject::class,
                AcceleratorPhase::class,
            ])
            ->get()
            ->map(fn ($vote) => $vote->created_at->timestamp - $vote->votable->created_at->timestamp)
            ->average();

        $pillar->az_avg_vote_time = $averageVoteTime;
        $pillar->save();
    }
}
