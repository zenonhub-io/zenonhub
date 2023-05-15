<?php

namespace App\Http\Controllers\Accelerator;

use App\Http\Controllers\PageController;
use App\Models\Nom\AcceleratorProject;

class Projects extends PageController
{
    public function show()
    {
        $this->page['meta']['title'] = 'Accelerator Z Projects';
        $this->page['meta']['description'] = 'An overview of all Accelerator Z projects submitted to date. Each project has a two week voting window to be voted on by pillars';

        return $this->render('pages/az/overview');
    }

    public function detail($hash)
    {
        $project = AcceleratorProject::findByHash($hash);

        if (! $project) {
            abort(404);
        }

        $this->page['meta']['title'] = 'Accelerator Project | '.$project->name;
        $this->page['meta']['description'] = "A detailed overview of project {$project->hash} see the funding request, description, voting status and phases";
        $this->page['data'] = [
            'project' => $project,
        ];

        return $this->render('pages/az/project');
    }
}
