<?php

declare(strict_types=1);

namespace App\Http\Livewire\Tables;

use App\Domains\Nom\Models\AcceleratorPhase;
use App\Domains\Nom\Models\Pillar;
use App\Http\Livewire\DataTableTrait;
use Livewire\Component;
use Livewire\WithPagination;

class PhaseVotes extends Component
{
    use DataTableTrait;
    use WithPagination;

    public AcceleratorPhase $phase;

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

        return view('livewire.tables.phase-votes', [
            'data' => $this->data,
        ]);
    }

    protected function initQuery()
    {
        $this->query = $this->phase->votes();
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
