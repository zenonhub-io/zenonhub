<?php

namespace App\Http\Livewire\Stats\Accelerator;

use App\Http\Livewire\ChartTrait;
use App\Models\Nom\AcceleratorProject;
use Livewire\Component;

class Projects extends Component
{
    use ChartTrait;

    public array $projectData;

    public function render()
    {
        return view('livewire.stats.accelerator.projects');
    }

    public function loadProjectData()
    {
        $this->projectData = [
            'labels' => ['New', 'Accepted', 'Completed', 'Rejected'],
            'data' => [
                AcceleratorProject::isNew()->count(),
                AcceleratorProject::isAccepted()->count(),
                AcceleratorProject::isCompleted()->count(),
                AcceleratorProject::isRejected()->count(),
            ],
        ];

        $this->emit('stats.az.projectDataLoaded', $this->projectData);
    }
}
