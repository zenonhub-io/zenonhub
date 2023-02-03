<?php

namespace App\Http\Livewire\Tables;

use App\Models\Nom\Momentum;
use Livewire\Component;

class MomentumBlocks extends Component
{
    use \Livewire\WithPagination;
    use \App\Http\Livewire\DataTableTrait;

    public Momentum $momentum;
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

        return view('livewire.tables.momentum-blocks', [
            'data' => $this->data
        ]);
    }

    public function export()
    {
        $exportName = "momentum-transactions-{$this->momentum->hash}.csv";
        $export = new \App\Exports\MomentumBlocks($this->momentum, $this->search, $this->sort, $this->order);

        return $this->doExport($export, $exportName);
    }

    protected function initQuery()
    {
        $this->query = $this->momentum->account_blocks();
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
