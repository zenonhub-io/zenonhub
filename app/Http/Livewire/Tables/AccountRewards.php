<?php

namespace App\Http\Livewire\Tables;

use App\Http\Livewire\DataTableTrait;
use App\Models\Nom\Account;
use App\Models\Nom\AccountReward;
use Livewire\Component;
use Livewire\WithPagination;

class AccountRewards extends Component
{
    use DataTableTrait;
    use WithPagination;

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
                $this->query->where('type', AccountReward::TYPE_DELEGATE);
            }

            if ($searchTerm === 'stake') {
                $this->query->where('type', AccountReward::TYPE_STAKE);
            }

            if ($searchTerm === 'pillar') {
                $this->query->where('type', AccountReward::TYPE_PILLAR);
            }

            if ($searchTerm === 'sentinel') {
                $this->query->where('type', AccountReward::TYPE_SENTINEL);
            }

            if ($searchTerm === 'liquidity') {
                $this->query->where('type', AccountReward::TYPE_LIQUIDITY);
            }

            if ($searchTerm === 'liquidity program') {
                $this->query->where('type', AccountReward::TYPE_LIQUIDITY_PROGRAM);
            }

            if ($searchTerm === 'bridge') {
                $this->query->where('type', AccountReward::TYPE_BRIDGE_AFFILIATE);
            }
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
                ->orderByRaw("CAST({$this->sort} AS UNSIGNED)".$this->order);
        } else {
            $this->query->orderBy($this->sort, $this->order);
        }
    }
}
