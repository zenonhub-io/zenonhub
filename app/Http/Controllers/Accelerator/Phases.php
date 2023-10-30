<?php

namespace App\Http\Controllers\Accelerator;

use App\Models\Nom\AcceleratorPhase;
use Meta;

class Phases
{
    public function detail($hash)
    {
        $phase = AcceleratorPhase::findByHash($hash);

        if (! $phase) {
            abort(404);
        }

        Meta::title("{$phase->name} - Phase details")
            ->description("Discover {$phase->name} phase of the {$phase->project->name} project, a venture powered by Accelerator-Z within the Network of Momentum ecosystem. Explore its funding status, votes and more");

        return view('pages/az/phase', [
            'phase' => $phase,
        ]);
    }
}
