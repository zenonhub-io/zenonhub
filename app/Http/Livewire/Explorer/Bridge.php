<?php

namespace App\Http\Livewire\Explorer;

use App\Http\Livewire\DataTableTrait;
use App\Models\Nom\BridgeUnwrap;
use App\Models\Nom\BridgeWrap;
use App\Models\Nom\Token;
use Livewire\Component;
use Livewire\WithPagination;

class Bridge extends Component
{
    use DataTableTrait;
    use WithPagination;

    public string $tab = 'inbound';

    protected $queryString = [
        'sort' => ['except' => 'created_at'],
        'order' => ['except' => 'desc'],
        'tab' => ['except' => 'inbound'],
        'filters' => ['except' => [
            'tokens' => [
                'ZNN',
                'QSR',
                'ZNNETHLP',
                'WBTC',
            ],
        ]],
    ];

    public function setTab($tab = 'inbound')
    {
        $this->tab = $tab;
        $this->resetPage();
    }

    public function mount()
    {
        $this->loadResults = true;
        $this->sort = request()->query('sort', 'created_at');
        $this->filters = request()->query('filters', [
            'tokens' => [
                'ZNN',
                'QSR',
                'ZNNETHLP',
                'WBTC',
            ],
        ]);
    }

    public function render()
    {
        $this->loadData();

        return view('livewire.explorer.bridge', [
            'data' => $this->data,
            'tokens' => Token::whereHas('bridge_network_tokens')->get(),
        ]);
    }

    protected function initQuery()
    {
        if ($this->tab === 'inbound') {
            $this->query = BridgeUnwrap::query();
        }

        if ($this->tab === 'outbound') {
            $this->query = BridgeWrap::query();
        }
    }

    protected function filterList()
    {
        if (! $this->query) {
            return;
        }

        $this->query->whereHas('token', fn ($q) => $q->whereIn('symbol', $this->filters['tokens']));
    }

    protected function sortList()
    {
        if (! $this->query) {
            return;
        }

        if ($this->sort === 'amount') {
            $this->query
                ->orderByRaw("{$this->sort} IS NULL ASC")
                ->orderByRaw("CAST({$this->sort} AS UNSIGNED)".$this->order);
        } else {
            $this->query->orderBy($this->sort, $this->order);
        }
    }
}
