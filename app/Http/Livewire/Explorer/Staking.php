<?php

namespace App\Http\Livewire\Explorer;

use App\Http\Livewire\DataTableTrait;
use App\Models\Nom\Staker;
use Livewire\Component;
use Livewire\WithPagination;

class Staking extends Component
{
    use WithPagination;
    use DataTableTrait;

    protected $queryString = [
        'sort' => ['except' => 'started_at'],
        'order' => ['except' => 'desc'],
    ];

    public function mount()
    {
        $this->loadResults = true;
        $this->sort = request()->query('sort', 'started_at');
    }

    public function render()
    {
        $this->loadData();

        return view('livewire.explorer.staking', [
            'data' => $this->data,
        ]);
    }

    protected function initQuery()
    {
        $this->query = Staker::isActive();
    }
}
