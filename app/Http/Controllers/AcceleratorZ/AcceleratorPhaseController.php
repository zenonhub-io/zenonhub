<?php

declare(strict_types=1);

namespace App\Http\Controllers\AcceleratorZ;

use App\Models\Nom\AcceleratorPhase;
use Illuminate\Contracts\View\View;
use MetaTags;

class AcceleratorPhaseController
{
    public function __invoke(string $hash, ?string $tab = 'votes'): View
    {
        $phase = AcceleratorPhase::firstWhere('hash', $hash)?->load('project');

        if (! $phase) {
            abort(404);
        }

        MetaTags::title(__(':phase_name - Accelerator-Z Phase Details', ['phase_name' => $phase->name]))
            ->description(__('Discover :phase_name phase of the :project_name project, funded by Accelerator-Z within the Network of Momentum ecosystem. Explore its funding status, votes and more', ['phase_name' => $phase->name, 'project_name' => $phase->project->name]))
            ->canonical(route('accelerator-z.phase.detail', ['hash' => $phase->hash]))
            ->metaByName('robots', 'index,follow');

        return view('accelerator-z.phase-detail', [
            'phase' => $phase,
            'tab' => $tab,
        ]);
    }
}
