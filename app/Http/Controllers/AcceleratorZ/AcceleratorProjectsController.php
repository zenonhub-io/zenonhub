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
            $title = __('Accelerator-Z Projects List: Fueling Innovation in the Network of Momentum');
            $description = __('Discover all Accelerator-Z projects fostering innovation within the Network of Momentum ecosystem. Explore project phases, voting details, and funding requests in one place');
            $canonical = route('accelerator-z.list');
        } else {
            $title = __(':tab Accelerator-Z Projects List: Fueling Innovation in the Network of Momentum', ['tab' => str($tab)->singular()->title()]);
            $description = __('Browse :tab Accelerator-Z projects fostering Network of Momentum innovation. Learn more about project phases, votes, and funding', ['tab' => $tab]);
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

        MetaTags::title(__(':project_name - Accelerator-Z Project Details', ['project_name' => $project->name]))
            ->description(__('Learn more about :project_name, a project funded by Accelerator-Z in the Network of Momentum. Explore its funding status, phases, votes, and other key details', ['project_name' => $project->name]))
            ->canonical(route('accelerator-z.project.detail', ['hash' => $project->hash]))
            ->metaByName('robots', 'index,follow');

        return view('accelerator-z.project-detail', [
            'project' => $project,
            'tab' => $tab,
        ]);
    }
}
