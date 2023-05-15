<?php

namespace App\Http\Livewire\Explorer;

use App\Models\Nom\Views\ViewLatestMomentum;
use Livewire\Component;

class Momentums extends Component
{
    use \Livewire\WithPagination;
    use \App\Http\Livewire\DataTableTrait;

    protected $queryString = [
        'sort' => ['except' => 'height'],
        'order' => ['except' => 'desc'],
    ];

    public function mount()
    {
        $this->loadResults = true;
        $this->sort = request()->query('sort', 'height');
    }

    public function render()
    {
        $this->loadData();

        return view('livewire.explorer.momentums', [
            'data' => $this->data,
        ]);
    }

    protected function initQuery()
    {
        $this->query = ViewLatestMomentum::withCount('account_blocks');
    }
}
