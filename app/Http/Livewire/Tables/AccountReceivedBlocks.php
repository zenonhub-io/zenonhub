<?php

namespace App\Http\Livewire\Tables;

use App\Models\Nom\Account;
use Livewire\Component;

class AccountReceivedBlocks extends Component
{
    use \Livewire\WithPagination;
    use \App\Http\Livewire\DataTableTrait;

    public Account $account;
    protected $queryString = [
        'sort' => ['except' => 'created_at'],
        'order' => ['except' => 'desc']
    ];

    public function mount()
    {
        $this->sort = request()->query('sort', 'created_at');
    }

    public function render()
    {
        $this->loadData();

        return view('livewire.tables.account-received-blocks', [
            'data' => $this->data
        ]);
    }

    public function export()
    {
        $exportName = "account-received-blocks-{$this->account->address}.csv";
        $export = new \App\Exports\AccountReceivedBlocks($this->account, $this->search, $this->sort, $this->order);

        return $this->doExport($export, $exportName);
    }

    protected function initQuery()
    {
        $this->query = $this->account->received_blocks();
    }

    protected function filterList()
    {
        if (! $this->query) {
            return;
        }

        if ($this->search) {
            $this->query->where(function ($q) {
                $q->where('height', $this->search);
                $q->orWhere('hash', $this->search);
                $q->orWhereHas('token', fn($q2) => $q2->where('name', $this->search));
                $q->orWhereHas('account', function ($q2) {
                    $q2->where('address', 'LIKE', "%{$this->search}%")
                        ->orWhere('name', 'LIKE', "%{$this->search}%");
                });
            });
            $this->resetPage();
        }
    }
}
