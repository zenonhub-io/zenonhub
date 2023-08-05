<?php

namespace App\Http\Livewire\Explorer;

use App\Http\Livewire\DataTableTrait;
use App\Models\Nom\Stake;
use Livewire\Component;
use Livewire\WithPagination;

class Staking extends Component
{
    use WithPagination;
    use DataTableTrait;

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
            $this->query->where('token_id', znn_token()->id);
        }

        if ($this->tab === 'lp-eth') {
            $this->query->where('token_id', lp_eth_token()->id);
        }
    }
}
