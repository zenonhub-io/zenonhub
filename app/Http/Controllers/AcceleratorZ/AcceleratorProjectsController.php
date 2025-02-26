<?php

declare(strict_types=1);

namespace App\Http\Controllers\AcceleratorZ;

use App\Models\Nom\AcceleratorProject;
use Illuminate\Contracts\View\View;
use MetaTags;

class AcceleratorProjectsController
{
    public function index(?string $tab = 'all'): View
    {
        if ($tab === 'all') {
            $title = 'Accelerator-Z Projects List: Fueling Innovation in the Network of Momentum';
            $description = 'Discover all Accelerator-Z projects fostering innovation within the Network of Momentum ecosystem. Explore project phases, voting details, and funding requests in one place.';
            $canonical = route('accelerator-z.list');
        } else {
            $title = sprintf('%s Accelerator-Z Projects List: Fueling Innovation in the Network of Momentum', str($tab)->singular()->title());
            $description = "Browse {$tab} Accelerator-Z projects fostering Network of Momentum innovation. Learn more about project phases, votes, and funding";
            $canonical = route('accelerator-z.list', ['tab' => $tab]);
        }

        MetaTags::title($title)
            ->description($description)
            ->canonical($canonical)
            ->metaByName('robots', 'index,follow');

        return view('accelerator-z.list', [
            'tab' => $tab,
        ]);
    }

    public function show(string $hash, ?string $tab = 'votes'): View
    {
        $project = AcceleratorProject::firstWhere('hash', $hash)?->load('phases');

        if (! $project) {
            abort(404);
        }

        MetaTags::title("{$project->name} - Accelerator-Z Project Details")
            ->description("Learn more about {$project->name}, a project funded by Accelerator-Z in the Network of Momentum. Explore its funding status, phases, votes, and other key details")
            ->canonical(route('accelerator-z.project.detail', ['hash' => $project->hash]))
            ->metaByName('robots', 'index,follow');

        return view('accelerator-z.project-detail', [
            'project' => $project,
            'tab' => $tab,
        ]);
    }
}
