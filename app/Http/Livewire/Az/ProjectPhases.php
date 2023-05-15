<?php

namespace App\Http\Livewire\Az;

use App\Models\Nom\AcceleratorProject;
use Livewire\Component;

class ProjectPhases extends Component
{
    use \Livewire\WithPagination;
    use \App\Http\Livewire\DataTableTrait;

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
