<?php

namespace App\Http\Livewire\Tables;

use Str;
use App\Models\Nom\Account;
use Livewire\Component;

class AccountSentBlocks extends Component
{
    use \Livewire\WithPagination;
    use \App\Http\Livewire\DataTableTrait;

    public Account $account;
    protected $queryString = [
        'sort' => ['except' => 'height'],
        'order' => ['except' => 'desc']
    ];

    public function mount()
    {
        $this->sort = request()->query('sort', 'height');
    }

    public function render()
    {
        $this->loadData();

        return view('livewire.tables.account-sent-blocks', [
            'data' => $this->data,
        ]);
    }

    public function export()
    {
        $exportName = "account-sent-blocks-{$this->account->address}.csv";
        $export = new \App\Exports\AccountSentBlocks($this->account, $this->search, $this->sort, $this->order);

        return $this->doExport($export, $exportName);
    }

    protected function initQuery()
    {
        $this->query = $this->account->sent_blocks();
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
                $q->orWhereHas('to_account', function ($q2) {
                    $q2->where('address', 'LIKE', "%{$this->search}%")
                        ->orWhere('name', 'LIKE', "%{$this->search}%");
                });
            });
            $this->resetPage();
        }
    }
}
