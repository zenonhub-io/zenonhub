<?php

namespace App\Http\Livewire\Tables;

use App\Http\Livewire\DataTableTrait;
use App\Models\Nom\Account;
use App\Models\Nom\Pillar;
use Livewire\Component;
use Livewire\WithPagination;

class PillarDelegators extends Component
{
    use WithPagination;
    use DataTableTrait;

    public Pillar $pillar;

    protected $queryString = [
        'sort' => ['except' => 'weight'],
        'order' => ['except' => 'desc'],
        'search',
    ];

    public function mount()
    {
        $this->perPage = 10;
        $this->sort = request()->query('sort', 'weight');
        $this->order = request()->query('order', 'desc');
        $this->search = request()->query('search');
    }

    public function render()
    {
        $this->loadData();

        return view('livewire.tables.pillar-delegators', [
            'data' => $this->data,
        ]);
    }

    public function export()
    {
        $exportName = "pillar-delegators-{$this->pillar->slug}.csv";
        $export = new \App\Exports\PillarActiveDelegators($this->pillar, $this->search, $this->sort, $this->order);

        return $this->doExport($export, $exportName);
    }

    protected function initQuery()
    {
        $this->query = $this->pillar->delegators()
            ->whereHas('account', function ($q) {
                $q->where('znn_balance', '>', '0');
            })
            ->whereNull('ended_at');
    }

    protected function filterList()
    {
        if (! $this->query) {
            return;
        }

        if ($this->search) {
            $this->query->whereHas('account', function ($q) {
                $q->where('address', 'LIKE', "%{$this->search}%");
                $q->orWhere('name', 'LIKE', "%{$this->search}%");
            });
            $this->resetPage();
        }
    }

    protected function sortList()
    {
        if (! $this->query) {
            return;
        }

        if ($this->sort === 'weight') {
            $this->query->orderBy(
                Account::select('znn_balance')->whereColumn('nom_accounts.id', 'nom_pillar_delegators.account_id'),
                $this->order
            );
        } else {
            $this->query->orderBy($this->sort, $this->order);
        }
    }
}
