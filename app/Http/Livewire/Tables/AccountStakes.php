<?php

namespace App\Http\Livewire\Tables;

use App\Models\Nom\Account;
use Livewire\Component;

class AccountStakes extends Component
{
    use \Livewire\WithPagination;
    use \App\Http\Livewire\DataTableTrait;

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
}
