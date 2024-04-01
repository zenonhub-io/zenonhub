<?php

declare(strict_types=1);

namespace App\Http\Livewire\Tables;

use App\Domains\Nom\Models\PillarMessage;
use App\Http\Livewire\DataTableTrait;
use Livewire\Component;
use Livewire\WithPagination;

class BroadcastMessages extends Component
{
    use DataTableTrait;
    use WithPagination;

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
