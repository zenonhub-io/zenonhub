<?php

namespace App\Http\Livewire\Explorer;

use App\Models\Nom\Token;
use Livewire\Component;

class Tokens extends Component
{
    use \Livewire\WithPagination;
    use \App\Http\Livewire\DataTableTrait;

    protected $queryString = [
        'sort' => ['except' => 'holders_count'],
        'order' => ['except' => 'desc'],
    ];

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
    }
}
