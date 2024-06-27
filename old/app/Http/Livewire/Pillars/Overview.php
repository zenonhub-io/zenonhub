<?php

declare(strict_types=1);

namespace App\Http\Livewire\Pillars;

use App\Domains\Nom\Models\Pillar;
use App\Http\Livewire\DataTableTrait;
use Livewire\Component;
use Livewire\WithPagination;

class Overview extends Component
{
    use DataTableTrait;
    use WithPagination;

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
            'pillars' => $this->data,
        ]);
    }

    public function setList($list)
    {
        if (in_array($list, $this->availableLists)) {
            $this->list = $list;
            $this->resetPage();
        }
    }

    protected function sortList()
    {
        if (! $this->query) {
            return;
        }

        if ($this->sort === 'az_avg_vote_time') {
            $this->query
                ->orderByRaw('! ISNULL(revoked_at), ISNULL(az_avg_vote_time), az_avg_vote_time ' . $this->order);
        } else {
            $this->query
                ->orderByRaw("! ISNULL(revoked_at), {$this->sort} {$this->order}");
        }
    }

    private function initQuery()
    {
        $this->query = Pillar::withCount(['delegators' => function ($q) {
            $q->whereHas('account', function ($q2) {
                $q2->where('znn_balance', '>', '0');
            })->whereNull('ended_at');
        }]);
    }

    private function filterList()
    {
        if ($this->list === 'active') {
            $this->query->whereActive()
                ->whereProducing();
        } elseif ($this->list === 'inactive') {
            $this->query->whereActive()
                ->whereNotProducing();
        } elseif ($this->list === 'revoked') {
            $this->query->whereRevoked();
        }

        if ($this->search) {
            $this->query->whereListSearch($this->search);
            $this->resetPage();
        }
    }
}
