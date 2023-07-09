<?php

namespace App\Http\Livewire\Stats\Accelerator;

use App\Http\Livewire\DataTableTrait;
use App\Models\Nom\AcceleratorProject;
use App\Models\Nom\Account;
use Livewire\Component;
use Livewire\WithPagination;

class Contributors extends Component
{
    use DataTableTrait;
    use WithPagination;

    public function mount()
    {
        $this->sort = request()->query('sort', 'projects_count');
    }

    public function render()
    {
        $this->loadContributorsData();

        return view('livewire.stats.accelerator.contributors', [
            'data' => $this->data,
        ]);
    }

    private function loadContributorsData()
    {
        $this->data = Account::whereHas('projects')
            ->withCount([
                'projects',
                'projects as new_projects_count' => fn ($query) => $query->where('status', AcceleratorProject::STATUS_NEW),
                'projects as accepted_projects_count' => fn ($query) => $query->where('status', AcceleratorProject::STATUS_ACCEPTED),
                'projects as completed_projects_count' => fn ($query) => $query->where('status', AcceleratorProject::STATUS_COMPLETE),
                'projects as rejected_projects_count' => fn ($query) => $query->where('status', AcceleratorProject::STATUS_REJECTED),
            ])
            ->orderBy($this->sort, $this->order)
            ->simplePaginate(10);
    }
}
