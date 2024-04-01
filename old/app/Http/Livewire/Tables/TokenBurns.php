<?php

declare(strict_types=1);

namespace App\Http\Livewire\Tables;

use App\Domains\Nom\Models\Token;
use App\Http\Livewire\DataTableTrait;
use Livewire\Component;
use Livewire\WithPagination;

class TokenBurns extends Component
{
    use DataTableTrait;
    use WithPagination;

    public Token $token;

    protected $queryString = [
        'sort' => ['except' => 'created_at'],
        'order' => ['except' => 'desc'],
        'search',
    ];

    public function mount()
    {
        $this->sort = request()->query('sort', 'created_at');
        $this->order = request()->query('order', 'desc');
        $this->search = request()->query('search');
    }

    public function render()
    {
        $this->loadData();

        return view('livewire.tables.token-burns', [
            'data' => $this->data,
            'token' => $this->token,
        ]);
    }

    public function export()
    {
        $exportName = "token-burns-{$this->token->symbol}.csv";
        $export = new \App\Exports\TokenBurns($this->token, $this->search, $this->sort, $this->order);

        return $this->doExport($export, $exportName);
    }

    protected function initQuery()
    {
        $this->query = $this->token->burns();
    }

    protected function filterList()
    {
        if (! $this->query) {
            return;
        }

        if ($this->search) {
            $this->query->whereHas('account', function ($q) {
                $q->where('address', 'LIKE', "%{$this->search}%");
                $q->orWhere('name', 'LIKE', "%{$this->search}%");
            });
            $this->resetPage();
        }
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
