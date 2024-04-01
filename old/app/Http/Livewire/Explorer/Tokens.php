<?php

declare(strict_types=1);

namespace App\Http\Livewire\Explorer;

use App\Domains\Nom\Models\Token;
use App\Http\Livewire\DataTableTrait;
use Livewire\Component;
use Livewire\WithPagination;

class Tokens extends Component
{
    use DataTableTrait;
    use WithPagination;

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
        if ($this->tab === 'favorites' && ! auth()->check()) {
            $this->tab = 'all';
        }

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

        if ($this->tab === 'favorites' && auth()->check()) {
            $this->query->whereHasFavorite(auth()->user());
        }
    }
}
