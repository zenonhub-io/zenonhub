<?php

namespace App\Http\Livewire\Tables;

use Str;
use App\Models\Nom\Token;
use Livewire\Component;

class TokenBurns extends Component
{
    use \Livewire\WithPagination;
    use \App\Http\Livewire\DataTableTrait;

    public Token $token;
    protected $queryString = [
        'sort' => ['except' => 'created_at'],
        'order' => ['except' => 'desc']
    ];

    public function mount()
    {
        $this->sort = request()->query('sort', 'created_at');
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
}
