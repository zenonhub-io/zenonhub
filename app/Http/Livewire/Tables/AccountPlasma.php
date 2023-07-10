<?php

namespace App\Http\Livewire\Tables;

use App\Http\Livewire\DataTableTrait;
use App\Models\Nom\Account;
use Livewire\Component;
use Livewire\WithPagination;

class AccountPlasma extends Component
{
    use WithPagination;
    use DataTableTrait;

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

        return view('livewire.tables.account-plasma', [
            'data' => $this->data,
        ]);
    }

    public function export()
    {
        $exportName = "account-plasma-{$this->account->address}.csv";
        $export = new \App\Exports\AccountPlasma($this->account, $this->search, $this->sort, $this->order);

        return $this->doExport($export, $exportName);
    }

    protected function initQuery()
    {
        $this->query = $this->account->plasma()
            ->whereNull('ended_at');
    }

    protected function filterList()
    {
        if (! $this->query) {
            return;
        }

        if ($this->search) {
            $this->query->where(function ($q) {
                $q->orWhereHas('to_account', function ($q2) {
                    $q2->where('address', 'LIKE', "%{$this->search}%")
                        ->orWhere('name', 'LIKE', "%{$this->search}%");
                });
            });
        }
    }
}
