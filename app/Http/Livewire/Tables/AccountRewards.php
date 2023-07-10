<?php

namespace App\Http\Livewire\Tables;

use App\Http\Livewire\DataTableTrait;
use App\Models\Nom\Account;
use Livewire\Component;
use Livewire\WithPagination;

class AccountRewards extends Component
{
    use WithPagination;
    use DataTableTrait;

    public Account $account;

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

        return view('livewire.tables.account-rewards', [
            'data' => $this->data,
        ]);
    }

    public function export()
    {
        $exportName = "account-rewards-{$this->account->address}.csv";
        $export = new \App\Exports\AccountRewards($this->account, $this->search, $this->sort, $this->order);

        return $this->doExport($export, $exportName);
    }

    protected function initQuery()
    {
        $this->query = $this->account->rewards();
    }

    protected function filterList()
    {
        if (! $this->query) {
            return;
        }

        if ($this->search) {
            $searchTerm = strtolower($this->search);

            if ($searchTerm === 'delegate') {
                $this->query->where('type', '1');
            } elseif ($searchTerm === 'stake') {
                $this->query->where('type', '2');
            } elseif ($searchTerm === 'pillar') {
                $this->query->where('type', '3');
            } elseif ($searchTerm === 'sentinel') {
                $this->query->where('type', '4');
            } elseif ($searchTerm === 'liquidity') {
                $this->query->where('type', '5');
            } elseif ($searchTerm === 'liquidity program') {
                $this->query->where('type', '6');
            }
        }
    }
}
