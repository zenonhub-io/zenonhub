<?php

declare(strict_types=1);

namespace App\Http\Livewire\Explorer;

use App\Domains\Nom\Models\Plasma;
use App\Http\Livewire\DataTableTrait;
use Livewire\Component;
use Livewire\WithPagination;

class Fusions extends Component
{
    use DataTableTrait;
    use WithPagination;

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

        return view('livewire.explorer.fusions', [
            'data' => $this->data,
        ]);
    }

    protected function initQuery()
    {
        $this->query = Plasma::isActive();
    }

    protected function sortList()
    {
        if (! $this->query) {
            return;
        }

        if ($this->sort === 'amount') {
            $this->query
                ->orderByRaw("{$this->sort} IS NULL ASC")
                ->orderByRaw("CAST({$this->sort} AS UNSIGNED)" . $this->order);
        } else {
            $this->query->orderBy($this->sort, $this->order);
        }
    }
}
