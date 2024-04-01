<?php

declare(strict_types=1);

namespace App\Http\Controllers\Accelerator;

use App\Domains\Nom\Models\AcceleratorProject;
use Meta;

class Projects
{
    public function show()
    {
        Meta::title('Accelerator-Z Projects: Fueling Innovation in the Network of Momentum')
            ->description('Explore the diverse array of innovative projects funded by Accelerator-Z within the Network of Momentum ecosystem. A list of all Accelerator-Z projects showing their phases, votes and funding request.');

        return view('pages/az/overview');
    }

    public function detail($hash)
    {
        $project = AcceleratorProject::findBy('hash', $hash);

        if (! $project) {
            abort(404);
        }

        Meta::title("{$project->name} - Project details")
            ->description("Discover {$project->name}, a venture powered by Accelerator-Z within the Network of Momentum ecosystem. Explore its status, phases votes and more.");

        return view('pages/az/project', [
            'project' => $project,
        ]);
    }
}
