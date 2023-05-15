<?php

namespace App\Http\Livewire\Tables;

use App\Models\Nom\Account;
use Livewire\Component;

class AccountProjects extends Component
{
    use \Livewire\WithPagination;
    use \App\Http\Livewire\DataTableTrait;

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

        return view('livewire.tables.account-projects', [
            'data' => $this->data,
        ]);
    }

    public function export()
    {
        $exportName = "account-projects-{$this->account->address}.csv";
        $export = new \App\Exports\AccountProjects($this->account, $this->search, $this->sort, $this->order);

        return $this->doExport($export, $exportName);
    }

    protected function initQuery()
    {
        $this->query = $this->account->projects();
    }

    protected function filterList()
    {
        if (! $this->query) {
            return;
        }

        if ($this->search) {
            $this->query->where(function ($q) {
                $q->where('name', 'LIKE', "%{$this->search}%");
                $q->orWhere('hash', 'LIKE', "%{$this->search}%");
            });
        }
    }
}
