<?php

declare(strict_types=1);

namespace App\Http\Controllers\Utilities;

use App\Domains\Nom\Models\AcceleratorPhase;
use App\Domains\Nom\Models\AcceleratorProject;
use App\Domains\Nom\Models\Pillar;

class MissingVotes
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
                'pillars' => Pillar::whereHas('azVotes')
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
                'pillars' => Pillar::whereHas('azVotes')
                    ->whereNotIn('id', $votingPillars)
                    ->orderByDesc('az_engagement')
                    ->get(),
            ];
        }

        return view('pages/az/missing-votes', [
            'projectData' => $projectData,
            'phaseData' => $phaseData,
        ]);
    }
}
