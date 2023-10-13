<?php

namespace App\Http\Controllers\Utilities;

use App\Http\Controllers\PageController;
use App\Models\Nom\AcceleratorPhase;
use App\Models\Nom\AcceleratorProject;
use App\Models\Nom\Pillar;

class MissingVotes extends PageController
{
    public function show()
    {
        $this->page['meta']['title'] = 'AZ Missing votes';

        $phaseData = [];
        $phases = AcceleratorPhase::isOpen()
            ->whereNotIn('hash', [
                'c1d667abca033ba7148e6448a661556f5d4046b68f6c31803d74ab22f19b6142',
            ])
            ->get();

        foreach ($phases as $phase) {
            $votingPillars = $phase->votes()->pluck('pillar_id');
            $phaseData[] = [
                'phase' => $phase,
                'pillars' => Pillar::whereHas('az_votes')
                    ->whereNotIn('id', $votingPillars)
                    ->orderByDesc('az_engagement')
                    ->get(),
            ];
        }

        $projectData = [];
        $projects = AcceleratorProject::isNew()->get();

        foreach ($projects as $project) {
            $votingPillars = $project->votes()->pluck('pillar_id');
            $projectData[] = [
                'project' => $project,
                'pillars' => Pillar::whereHas('az_votes')
                    ->whereNotIn('id', $votingPillars)
                    ->orderByDesc('az_engagement')
                    ->get(),
            ];
        }

        return $this->render('pages/az/missing-votes', [
            'projectData' => $projectData,
            'phaseData' => $phaseData,
        ]);
    }
}
