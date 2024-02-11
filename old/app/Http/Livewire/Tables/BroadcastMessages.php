<?php

namespace App\Http\Livewire\Tables;

use App\Http\Livewire\DataTableTrait;
use App\Models\Nom\PillarMessage;
use Livewire\Component;
use Livewire\WithPagination;

class BroadcastMessages extends Component
{
    use WithPagination;
    use DataTableTrait;

    protected $queryString = [
        'sort' => ['except' => 'id'],
        'order' => ['except' => 'desc'],
    ];

    public function mount()
    {
        $this->perPage = 10;
    }

    public function render()
    {
        $this->loadData();

        return view('livewire.tables.broadcast-messages', [
            'data' => $this->data,
        ]);
    }

    protected function initQuery()
    {
        $this->query = PillarMessage::query();
    }
}
