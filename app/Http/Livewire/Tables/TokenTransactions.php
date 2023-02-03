<?php

namespace App\Http\Livewire\Tables;

use App\Models\Nom\Token;
use Livewire\Component;

class TokenTransactions extends Component
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

        return view('livewire.tables.token-transactions', [
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
        $this->query = $this->token->transactions();
    }

    protected function filterList()
    {
        if (! $this->query) {
            return;
        }

        if ($this->search) {
            $this->query->where(function ($q) {
                $q->where('height', $this->search);
                $q->orWhere('hash', $this->search);
                $q->orWhereHas('token', fn($q2) => $q2->where('name', $this->search));
                $q->orWhereHas('account', function ($q2) {
                    $q2->where('address', 'LIKE', "%{$this->search}%")
                        ->orWhere('name', 'LIKE', "%{$this->search}%");
                });
                $q->orWhereHas('to_account', function ($q2) {
                    $q2->where('address', 'LIKE', "%{$this->search}%")
                        ->orWhere('name', 'LIKE', "%{$this->search}%");
                });
            });
            $this->resetPage();
        }
    }
}
