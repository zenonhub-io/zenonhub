<?php

namespace App\Http\Livewire\Tables;

use App\Models\Nom\Account;
use Livewire\Component;

class AccountTokens extends Component
{
    use \Livewire\WithPagination;
    use \App\Http\Livewire\DataTableTrait;

    public Account $account;
    protected $queryString = [
        'sort' => ['except' => 'default'],
        'order' => ['except' => 'desc']
    ];

    public function mount()
    {
        $this->sort = request()->query('sort', 'default');
    }

    public function render()
    {
        $this->loadData();

        return view('livewire.tables.account-tokens', [
            'data' => $this->data
        ]);
    }

    public function export()
    {
        $exportName = "account-tokens-{$this->account->address}.csv";
        $export = new \App\Exports\AccountTokens($this->account, $this->search, $this->sort, $this->order);

        return $this->doExport($export, $exportName);
    }

    protected function initQuery()
    {
        $this->query = $this->account->balances()
            ->wherePivot('balance', '>', '0');
    }

    protected function filterList()
    {
        if (! $this->query) {
            return;
        }

        if ($this->search) {
            $this->query->where('name', $this->search);
            $this->resetPage();
        }
    }

    protected function sortList()
    {
        if (! $this->query) {
            return;
        }

        if ($this->sort === 'default') {
            if ($this->order === 'desc') {
                $this->query->orderByRaw('(token_id = 6) desc')
                    ->orderByRaw('(token_id = 3) desc')
                    ->orderBy('balance', 'DESC');
            } else {
                $this->query->orderByRaw('(token_id = 6) desc')
                    ->orderByRaw('(token_id = 3) desc')
                    ->orderBy('balance', 'ASC');
            }
        } else {
            $this->query->orderBy($this->sort, $this->order);
        }
    }
}
