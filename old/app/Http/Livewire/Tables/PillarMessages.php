<?php

declare(strict_types=1);

namespace App\Http\Livewire\Tables;

use App\Domains\Nom\Models\Pillar;
use App\Http\Livewire\DataTableTrait;
use Livewire\Component;
use Livewire\WithPagination;

class PillarMessages extends Component
{
    use DataTableTrait;
    use WithPagination;

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
