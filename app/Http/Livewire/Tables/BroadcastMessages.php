<?php

namespace App\Http\Livewire\Tables;

use App\Models\PillarMessage;
use Livewire\Component;

class BroadcastMessages extends Component
{
    use \Livewire\WithPagination;
    use \App\Http\Livewire\DataTableTrait;

    protected $queryString = [
        'sort' => ['except' => 'id'],
        'order' => ['except' => 'desc']
    ];

    public function mount()
    {
        $this->perPage = 10;
    }

    public function render()
    {
        $this->loadData();

        return view('livewire.tables.broadcast-messages', [
            'data' => $this->data
        ]);
    }

    protected function initQuery()
    {
        $this->query = PillarMessage::query();
    }
}
