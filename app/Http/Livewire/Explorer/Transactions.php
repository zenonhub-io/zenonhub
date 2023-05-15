<?php

namespace App\Http\Livewire\Explorer;

use App\Models\Nom\Views\ViewLatestAccountBlock;
use Livewire\Component;

class Transactions extends Component
{
    use \Livewire\WithPagination;
    use \App\Http\Livewire\DataTableTrait;

    protected $queryString = [
        'sort' => ['except' => 'created_at'],
        'order' => ['except' => 'desc'],
    ];

    public function mount()
    {
        $this->loadResults = true;
        $this->sort = request()->query('sort', 'created_at');
    }

    public function render()
    {
        $this->loadData();

        return view('livewire.explorer.transactions', [
            'data' => $this->data,
        ]);
    }

    protected function initQuery()
    {
        $this->query = ViewLatestAccountBlock::query();
    }
}
