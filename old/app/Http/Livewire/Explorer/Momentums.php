<?php

declare(strict_types=1);

namespace App\Http\Livewire\Explorer;

use App\Domains\Nom\Models\Views\ViewLatestMomentum;
use App\Http\Livewire\DataTableTrait;
use Livewire\Component;
use Livewire\WithPagination;

class Momentums extends Component
{
    use DataTableTrait;
    use WithPagination;

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
        $this->query = ViewLatestMomentum::withCount('accountBlocks');
    }
}
