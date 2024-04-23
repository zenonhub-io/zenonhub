<?php

declare(strict_types=1);

namespace App\Http\Livewire\Explorer;

use App\Domains\Nom\Models\Stake;
use App\Http\Livewire\DataTableTrait;
use Livewire\Component;
use Livewire\WithPagination;

class Staking extends Component
{
    use DataTableTrait;
    use WithPagination;

    public string $tab = 'all';

    protected $queryString = [
        'sort' => ['except' => 'started_at'],
        'order' => ['except' => 'desc'],
        'tab' => ['except' => 'all'],
    ];

    public function setTab($tab = 'all')
    {
        $this->tab = $tab;
        $this->resetPage();
    }

    public function mount()
    {
        $this->loadResults = true;
        $this->sort = request()->query('sort', 'started_at');
    }

    public function render()
    {
        $this->loadData();

        return view('livewire.explorer.staking', [
            'data' => $this->data,
        ]);
    }

    protected function initQuery()
    {
        $this->query = Stake::isActive();

        if ($this->tab === 'znn') {
            $this->query->where('token_id', app('znnToken')->id);
        }

        if ($this->tab === 'lp-eth') {
            $this->query->where('token_id', lp_eth_token()->id);
        }
    }

    protected function sortList()
    {
        if (! $this->query) {
            return;
        }

        if ($this->sort === 'amount') {
            $this->query
                ->orderByRaw("{$this->sort} IS NULL ASC")
                ->orderByRaw("CAST({$this->sort} AS UNSIGNED)" . $this->order);
        } else {
            $this->query->orderBy($this->sort, $this->order);
        }
    }
}
