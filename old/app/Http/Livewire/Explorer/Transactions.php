<?php

declare(strict_types=1);

namespace App\Http\Livewire\Explorer;

use App\Domains\Nom\Models\Views\ViewLatestAccountBlock;
use App\Http\Livewire\DataTableTrait;
use Livewire\Component;
use Livewire\WithPagination;

class Transactions extends Component
{
    use DataTableTrait;
    use WithPagination;

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
