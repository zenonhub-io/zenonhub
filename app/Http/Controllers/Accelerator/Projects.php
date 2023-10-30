<?php

namespace App\Http\Controllers\Accelerator;

use App\Models\Nom\AcceleratorProject;
use Meta;

class Projects
{
    public function show()
    {
        Meta::title('Accelerator-Z Projects: Fueling Innovation in the Network of Momentum')
            ->description('Explore the diverse array of innovative projects supported by Accelerator-Z within the Network of Momentum ecosystem. Discover the cutting-edge developments and groundbreaking initiatives driving the future of blockchain and Web3 technology.');

        return view('pages/az/overview');
    }

    public function detail($hash)
    {
        $project = AcceleratorProject::findByHash($hash);

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
