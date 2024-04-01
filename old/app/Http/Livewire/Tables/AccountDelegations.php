<?php

declare(strict_types=1);

namespace App\Http\Livewire\Tables;

use App\Domains\Nom\Models\Account;
use App\Http\Livewire\DataTableTrait;
use Livewire\Component;
use Livewire\WithPagination;

class AccountDelegations extends Component
{
    use DataTableTrait;
    use WithPagination;

    public Account $account;

    protected $queryString = [
        'sort' => ['except' => 'started_at'],
        'order' => ['except' => 'desc'],
        'search',
    ];

    public function mount()
    {
        $this->sort = request()->query('sort', 'started_at');
        $this->order = request()->query('order', 'desc');
        $this->search = request()->query('search');
    }

    public function render()
    {
        $this->loadData();

        return view('livewire.tables.account-delegations', [
            'data' => $this->data,
        ]);
    }

    public function export()
    {
        $exportName = "account-delegation-{$this->account->address}.csv";
        $export = new \App\Exports\AccountDelegations($this->account, $this->search, $this->sort, $this->order);

        return $this->doExport($export, $exportName);
    }

    protected function initQuery()
    {
        $this->query = $this->account->delegations();
    }

    protected function filterList()
    {
        if (! $this->query) {
            return;
        }

        if ($this->search) {
            $this->query->whereHas('pillar', function ($q) {
                $q->where('name', 'LIKE', "%{$this->search}%");
            });
        }
    }
}
