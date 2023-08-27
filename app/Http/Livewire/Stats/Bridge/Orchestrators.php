<?php

namespace App\Http\Livewire\Stats\Bridge;

use App\Http\Livewire\DataTableTrait;
use App\Models\Nom\Orchestrator;
use Livewire\Component;
use Livewire\WithPagination;

class Orchestrators extends Component
{
    use WithPagination;
    use DataTableTrait;

    public function mount()
    {
        $this->sort = 'nom_pillars.name';
        $this->order = 'asc';
    }

    public function render()
    {
        $this->loadOrchestratorData();

        return view('livewire.stats.bridge.orchestrators', [
            'data' => $this->data,
        ]);
    }

    private function loadOrchestratorData()
    {
        $this->data = Orchestrator::join(
            'nom_pillars',
            'nom_orchestrators.pillar_id',
            '=',
            'nom_pillars.id'
        )
            ->orderBy($this->sort, $this->order)
            ->paginate(10);
    }
}
