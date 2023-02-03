<?php

namespace App\Http\Livewire\Explorer;

use App\Models\Nom\Account;
use Livewire\Component;

class Accounts extends Component
{
    use \Livewire\WithPagination;
    use \App\Http\Livewire\DataTableTrait;

    protected $queryString = [
        'sort' => ['except' => 'znn_balance'],
        'order' => ['except' => 'desc']
    ];

    public function mount()
    {
        $this->loadResults = true;
        $this->sort = request()->query('sort', 'znn_balance');
    }

    public function render()
    {
        $this->loadData();

        return view('livewire.explorer.accounts', [
            'data' => $this->data
        ]);
    }

    protected function initQuery()
    {
        $this->query = Account::query();
    }
}
