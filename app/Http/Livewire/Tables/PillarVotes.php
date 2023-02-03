<?php

namespace App\Http\Livewire\Tables;

use App\Models\Nom\AcceleratorProject;
use App\Models\Nom\Pillar;
use Livewire\Component;

class PillarVotes extends Component
{
    use \Livewire\WithPagination;
    use \App\Http\Livewire\DataTableTrait;

    public Pillar $pillar;
    protected $queryString = [
        'sort' => ['except' => 'created_at'],
        'order' => ['except' => 'desc']
    ];

    public function mount()
    {
        $this->sort = request()->query('sort', 'created_at');
        $this->perPage = 10;
    }

    public function render()
    {
        $this->loadData();

        return view('livewire.tables.pillar-votes', [
            'data' => $this->data
        ]);
    }

    public function export()
    {
        $exportName = "pillar-votes-{$this->pillar->slug}.csv";
        $export = new \App\Exports\PillarVotes($this->pillar, $this->search, $this->sort, $this->order);

        return $this->doExport($export, $exportName);
    }

    protected function initQuery()
    {
        $this->query = $this->pillar->az_project_votes();
    }

    protected function filterList()
    {
        if (! $this->query) {
            return;
        }

        if ($this->search) {
            $this->query->whereHas('project', function ($q) {
                $q->where('name', 'LIKE', "%{$this->search}%");
            });
            $this->resetPage();
        }
    }

    protected function sortList()
    {
        if (! $this->query) {
            return;
        }

        if ($this->sort === 'project') {
            $this->query->orderBy(
                AcceleratorProject::select('name')->whereColumn('nom_accelerator_projects.id', 'nom_accelerator_project_votes.accelerator_project_id'),
                $this->order
            );
        } else {
            $this->query->orderBy($this->sort, $this->order);
        }
    }
}
