<?php

namespace App\Http\Livewire\Tables;

use App\Http\Livewire\DataTableTrait;
use App\Models\Nom\Pillar;
use Livewire\Component;
use Livewire\WithPagination;

class PillarMessages extends Component
{
    use WithPagination;
    use DataTableTrait;

    public Pillar $pillar;

    protected $queryString = [
        'sort' => ['except' => 'id'],
        'order' => ['except' => 'desc'],
        'search',
    ];

    public function mount()
    {
        $this->perPage = 10;
    }

    public function render()
    {
        $this->loadData();

        return view('livewire.tables.pillar-messages', [
            'data' => $this->data,
        ]);
    }

    protected function initQuery()
    {
        $this->query = $this->pillar->messages();
    }
}
