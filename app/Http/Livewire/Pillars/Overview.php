<?php

namespace App\Http\Livewire\Pillars;

use App\Http\Livewire\DataTableTrait;
use App\Models\Nom\Pillar;
use Livewire\Component;
use Livewire\WithPagination;

class Overview extends Component
{
    use WithPagination;
    use DataTableTrait;

    public string $list = 'all';

    public $availableLists = [
        'all',
        'active',
        'inactive',
        'revoked',
        'favorites',
    ];

    protected $queryString = [
        'search',
        'list' => ['except' => 'all'],
    ];

    public function mount()
    {
        $this->loadResults = true;
        $this->sort = 'weight';
        $this->perPage = 30;
    }

    public function render()
    {
        $this->loadData();

        return view('livewire.pillars.overview', [
            'pillars' => $this->data
        ]);
    }

    public function setList($list)
    {
        if (in_array($list, $this->availableLists)) {
            $this->list = $list;
            $this->resetPage();
        }
    }

    private function initQuery()
    {
        $this->query = Pillar::withCount(['delegators' => function($q) {
            $q->whereHas('account', function ($q2) {
                $q2->where('znn_balance', '>', '0');
            })->whereNull('ended_at');
        }]);
    }

    private function filterList()
    {
        if ($this->list === 'active') {
            $this->query->isActive()
                ->isProducing();
        } elseif ($this->list === 'inactive') {
            $this->query->isActive()
                ->isNotProducing();
        } elseif ($this->list === 'revoked') {
            $this->query->isRevoked();
        }

        if ($this->search) {
            $this->query->whereListSearch($this->search);
            $this->resetPage();
        }
    }

    protected function sortList()
    {
        if (! $this->query) {
            return;
        }

        $this->query->orderBy($this->sort, $this->order);
    }
}
