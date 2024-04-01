<?php

declare(strict_types=1);

namespace App\Http\Livewire\Tables;

use App\Domains\Nom\Models\Account;
use App\Http\Livewire\DataTableTrait;
use Livewire\Component;
use Livewire\WithPagination;

class AccountStakes extends Component
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

        return view('livewire.tables.account-stakes', [
            'data' => $this->data,
        ]);
    }

    public function export()
    {
        $exportName = "account-stakes-{$this->account->address}.csv";
        $export = new \App\Exports\AccountStakes($this->account, $this->search, $this->sort, $this->order);

        return $this->doExport($export, $exportName);
    }

    protected function initQuery()
    {
        $this->query = $this->account->stakes()
            ->whereNull('ended_at');
    }

    protected function filterList()
    {
        if (! $this->query) {
            return;
        }

        if ($this->search) {
            $this->query->where('amount', (is_numeric($this->search) ? $this->search * 100000000 : '0'));
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
