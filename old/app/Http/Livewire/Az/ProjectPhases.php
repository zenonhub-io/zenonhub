<?php

declare(strict_types=1);

namespace App\Http\Livewire\Az;

use App\Domains\Nom\Models\AcceleratorProject;
use Livewire\Component;

class ProjectPhases extends Component
{
    use \App\Http\Livewire\DataTableTrait;
    use \Livewire\WithPagination;

    public AcceleratorProject $project;

    public function mount()
    {
        $this->simplePaginate = true;
        $this->loadResults = true;
        $this->perPage = 10;
    }

    public function render()
    {
        $this->loadData();

        return view('livewire.az.project-phases', [
            'data' => $this->data,
        ]);
    }

    protected function initQuery()
    {
        $this->query = $this->project->phases();
    }
}
