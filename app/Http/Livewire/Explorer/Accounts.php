<?php

namespace App\Http\Livewire\Explorer;

use App\Http\Livewire\DataTableTrait;
use App\Models\Nom\Account;
use Livewire\Component;
use Livewire\WithPagination;

class Accounts extends Component
{
    use WithPagination;
    use DataTableTrait;

    public string $tab = 'all';

    protected $queryString = [
        'sort' => ['except' => 'znn_balance'],
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
        $this->sort = request()->query('sort', 'znn_balance');
    }

    public function render()
    {
        $this->loadData();

        return view('livewire.explorer.accounts', [
            'data' => $this->data,
        ]);
    }

    protected function initQuery()
    {
        $this->query = Account::withCount('sent_blocks');

        if ($this->tab === 'contracts') {
            $this->query->where('is_embedded_contract', '1');
        }

        if ($this->tab === 'pillars') {
            $this->query->whereIn('id', function ($query) {
                $query->select('owner_id')
                    ->from('nom_pillars')
                    ->whereNull('revoked_at');
            });
        }

        if ($this->tab === 'sentinels') {
            $this->query->whereIn('id', function ($query) {
                $query->select('owner_id')
                    ->from('nom_sentinels')
                    ->whereNull('revoked_at');
            });
        }

        if ($this->tab === 'favorites' && auth()->check()) {
            $this->query->whereHasFavorite(auth()->user());
        }
    }
}
