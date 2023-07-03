<?php

namespace App\Http\Livewire\Tables;

use App\Http\Livewire\DataTableTrait;
use App\Models\Markable\Favorite;
use App\Models\Nom\AccountBlock;
use Livewire\Component;
use Livewire\WithPagination;

class FavouriteTransactions extends Component
{
    use WithPagination;
    use DataTableTrait;

    protected $queryString = [
        'sort' => ['except' => 'created_at'],
        'order' => ['except' => 'desc'],
        'search',
    ];

    public function mount()
    {
        $this->sort = request()->query('sort', 'created_at');
        $this->order = request()->query('order', 'desc');
        $this->search = request()->query('search');
    }

    public function render()
    {
        $this->loadData();

        return view('livewire.tables.favourite-transactions', [
            'data' => $this->data,
        ]);
    }

    protected function initQuery()
    {
        $user = auth()->user();
        $this->query = Favorite::where('user_id', $user->id)
            ->whereHasMorph('markable', [
                AccountBlock::class,
            ]);
    }
}
