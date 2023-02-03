<?php

namespace App\Http\Livewire\Tables;

use App\Models\Nom\AccountBlock;
use Livewire\Component;

class BlockDescendants extends Component
{
    use \Livewire\WithPagination;
    use \App\Http\Livewire\DataTableTrait;

    public AccountBlock $transaction;
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

        return view('livewire.tables.block-descendants', [
            'data' => $this->data
        ]);
    }

    public function export()
    {
        $exportName = "transaction-descendants-{$this->transaction->hash}.csv";
        $export = new \App\Exports\BlockDescendants($this->transaction, $this->search, $this->sort, $this->order);

        return $this->doExport($export, $exportName);
    }

    protected function initQuery()
    {
        $this->query = $this->transaction->descendants();
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
                $q->orWhereHas('to_account', function ($q2) {
                    $q2->where('address', 'LIKE', "%{$this->search}%")
                        ->orWhere('name', 'LIKE', "%{$this->search}%");
                });
            });
            $this->resetPage();
        }
    }
}
