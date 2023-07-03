<?php

namespace App\Http\Livewire\Explorer;

use App\Http\Livewire\DataTableTrait;
use App\Models\Nom\Token;
use Livewire\Component;
use Livewire\WithPagination;

class Tokens extends Component
{
    use WithPagination;
    use DataTableTrait;

    public string $tab = 'all';

    protected $queryString = [
        'sort' => ['except' => 'holders_count'],
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
        $this->sort = request()->query('sort', 'holders_count');
    }

    public function render()
    {
        $this->loadData();

        return view('livewire.explorer.tokens', [
            'data' => $this->data,
        ]);
    }

    protected function initQuery()
    {
        $this->query = Token::withCount(['holders' => function ($q) {
            $q->where('balance', '>', '0');
        }]);

        if ($this->tab === 'network') {
            $this->query->whereHas('owner', fn ($query) => $query->where('is_embedded_contract', '1'));
        }

        if ($this->tab === 'user') {
            $this->query->whereHas('owner', fn ($query) => $query->where('is_embedded_contract', '0'));
        }

        if ($this->tab === 'favorites') {
            $this->query->whereHasFavorite(auth()->user());
        }
    }
}
