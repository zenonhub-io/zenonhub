<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Nom\AcceleratorProject;
use Illuminate\Contracts\View\View;
use MetaTags;

class AcceleratorProjectController
{
    private string $defaultTab = 'votes';

    public function __invoke(string $hash, ?string $tab = null): View
    {
        $project = AcceleratorProject::firstWhere('hash', $hash)?->load('phases');

        if (! $project) {
            abort(404);
        }

        MetaTags::title("{$project->name} - Project details")
            ->description("Discover {$project->name}, a venture powered by Accelerator-Z within the Network of Momentum ecosystem. Explore its status, phases votes and more.");

        return view('accelerator-z.project-detail', [
            'project' => $project,
            'tab' => $tab ?: $this->defaultTab,
        ]);
    }
}
