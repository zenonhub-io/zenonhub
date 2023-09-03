<?php

namespace App\Http\Livewire\Tables;

use App\Http\Livewire\DataTableTrait;
use App\Models\Nom\Token;
use Livewire\Component;
use Livewire\WithPagination;

class TokenTransactions extends Component
{
    use WithPagination;
    use DataTableTrait;

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

        return view('livewire.tables.transactions', [
            'data' => $this->data,
        ]);
    }

    public function export()
    {
        $exportName = "token-transactions-{$this->token->symbol}.csv";
        $export = new \App\Exports\TokenTransactions($this->token, $this->search, $this->sort, $this->order);

        return $this->doExport($export, $exportName);
    }

    protected function initQuery()
    {
        $this->query = $this->token
            ->transactions()
            ->where('amount', '>', '0');
    }

    protected function filterList()
    {
        if (! $this->query) {
            return;
        }

        if ($this->search) {
            $this->query->where(function ($q) {
                $q->where('height', $this->search)
                    ->orWhere('hash', $this->search)
                    ->orWhereHas('token', fn ($q2) => $q2->where('name', $this->search))
                    ->orWhereHas('account', fn ($q3) => $q3->where('address', $this->search))
                    ->orWhereHas('to_account', fn ($q4) => $q4->where('address', $this->search));
            });
            $this->resetPage();
        }
    }
}
