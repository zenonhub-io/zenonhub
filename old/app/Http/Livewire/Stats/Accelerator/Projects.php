<?php

declare(strict_types=1);

namespace App\Http\Livewire\Stats\Accelerator;

use App\Domains\Nom\Models\AcceleratorProject;
use App\Http\Livewire\ChartTrait;
use Illuminate\Support\Facades\Cache;
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
        $cacheExpiry = (60 * 60);
        $this->projectData = Cache::remember('stats.az.projectTotals', $cacheExpiry, function () {
            return [
                'labels' => ['New', 'Accepted', 'Completed', 'Rejected'],
                'data' => [
                    AcceleratorProject::isNew()->count(),
                    AcceleratorProject::isAccepted()->count(),
                    AcceleratorProject::isCompleted()->count(),
                    AcceleratorProject::isRejected()->count(),
                ],
            ];
        });

        $this->emit('stats.az.projectDataLoaded', $this->projectData);
    }
}
