<?php

declare(strict_types=1);

namespace App\Http\Livewire\Tables;

use App\Domains\Nom\Models\AcceleratorProject;
use App\Domains\Nom\Models\Pillar;
use App\Http\Livewire\DataTableTrait;
use Livewire\Component;
use Livewire\WithPagination;

class ProjectVotes extends Component
{
    use DataTableTrait;
    use WithPagination;

    public AcceleratorProject $project;

    protected $queryString = [
        'sort' => ['except' => 'created_at'],
        'order' => ['except' => 'desc'],
    ];

    public function mount()
    {
        $this->loadResults = true;
        $this->sort = request()->query('sort', 'created_at');
        $this->perPage = 10;
    }

    public function render()
    {
        $this->loadData();

        return view('livewire.tables.project-votes', [
            'data' => $this->data,
        ]);
    }

    protected function initQuery()
    {
        $this->query = $this->project->votes();
    }

    protected function sortList()
    {
        if (! $this->query) {
            return;
        }

        if ($this->sort === 'pillar') {
            $this->query->orderBy(
                Pillar::select('name')->whereColumn('nom_pillars.owner_id', 'nom_accelerator_votes.owner_id'),
                $this->order
            );
        } else {
            $this->query->orderBy($this->sort, $this->order);
        }
    }
}