<?php

namespace App\Http\Livewire\Tables;

use App\Models\Nom\Token;
use Livewire\Component;

class TokenHolders extends Component
{
    use \Livewire\WithPagination;
    use \App\Http\Livewire\DataTableTrait;

    public Token $token;

    protected $queryString = [
        'sort' => ['except' => 'balance'],
        'order' => ['except' => 'desc'],
        'search',
    ];

    public function mount()
    {
        $this->sort = request()->query('sort', 'balance');
        $this->order = request()->query('order', 'desc');
        $this->search = request()->query('search');
    }

    public function render()
    {
        $this->loadData();

        return view('livewire.tables.token-holders', [
            'data' => $this->data,
            'token' => $this->token,
        ]);
    }

    public function export()
    {
        $exportName = "token-holders-{$this->token->symbol}.csv";
        $export = new \App\Exports\TokenHolders($this->token, $this->search, $this->sort, $this->order);

        return $this->doExport($export, $exportName);
    }

    protected function initQuery()
    {
        $this->query = $this->token->holders()
            ->wherePivot('balance', '>', '0');
    }

    protected function filterList()
    {
        if (! $this->query) {
            return;
        }

        if ($this->search) {
            $this->query->where(function ($q) {
                $q->where('address', 'LIKE', "%{$this->search}%")
                    ->orWhere('name', 'LIKE', "%{$this->search}%");
            });
            $this->resetPage();
        }
    }
}
