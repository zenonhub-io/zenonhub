<?php

namespace App\Http\Livewire\Tables;

use App\Http\Livewire\DataTableTrait;
use App\Models\Nom\Account;
use Livewire\Component;
use Livewire\WithPagination;

class AccountBlocks extends Component
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

        return view('livewire.tables.account-blocks', [
            'data' => $this->data,
        ]);
    }

    public function export()
    {
        $exportName = "account-blocks-{$this->account->address}.csv";
        $export = new \App\Exports\AccountBlocks($this->account, $this->search, $this->sort, $this->order);

        return $this->doExport($export, $exportName);
    }

    protected function initQuery()
    {
        $query = $this->account->blocks();

        if ($this->account->address !== Account::ADDRESS_EMPTY) {
            $query->notToEmpty();
        }

        $this->query = $query;
    }

    protected function filterList()
    {
        if (! $this->query) {
            return;
        }

        if ($this->search) {
            $this->query->where(function ($q) {
                $q->where('height', $this->search)
                    ->orWhere('hash', $this->search)
                    ->orWhereHas('token', fn ($q2) => $q2->where('name', $this->search))
                    ->orWhereHas('account', fn ($q3) => $q3->where('address', $this->search))
                    ->orWhereHas('to_account', fn ($q4) => $q4->where('address', $this->search));
            });
        }
    }
}
