<?php

declare(strict_types=1);

namespace App\Http\Livewire\Stats\Bridge;

use App\Domains\Nom\Models\Orchestrator;
use App\Http\Livewire\DataTableTrait;
use Livewire\Component;
use Livewire\WithPagination;

class Orchestrators extends Component
{
    use DataTableTrait;
    use WithPagination;

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
