<?php

declare(strict_types=1);

namespace App\Http\Livewire\Tables;

use App\Domains\Nom\Models\Account;
use App\Http\Livewire\DataTableTrait;
use Livewire\Component;
use Livewire\WithPagination;

class AccountTokens extends Component
{
    use DataTableTrait;
    use WithPagination;

    public Account $account;

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

        return view('livewire.tables.account-tokens', [
            'data' => $this->data,
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

        $this->query->orderBy($this->sort, $this->order);
    }
}
