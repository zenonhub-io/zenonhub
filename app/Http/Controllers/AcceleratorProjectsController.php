<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Nom\AcceleratorProject;
use Illuminate\Contracts\View\View;
use MetaTags;

class AcceleratorProjectsController
{
    public function index(?string $tab = 'all'): View
    {
        MetaTags::title('Accelerator-Z Projects: Fueling Innovation in the Network of Momentum')
            ->description('Explore the diverse array of innovative projects funded by Accelerator-Z within the Network of Momentum ecosystem. A list of all Accelerator-Z projects showing their phases, votes and funding request.');

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

        MetaTags::title("{$project->name} - Project details")
            ->description("Discover {$project->name}, a venture powered by Accelerator-Z within the Network of Momentum ecosystem. Explore its status, phases votes and more.");

        return view('accelerator-z.project-detail', [
            'project' => $project,
            'tab' => $tab,
        ]);
    }
}
