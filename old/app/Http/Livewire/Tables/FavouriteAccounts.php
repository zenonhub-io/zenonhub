<?php

declare(strict_types=1);

namespace App\Http\Livewire\Tables;

use App\Domains\Nom\Models\Account;
use App\Http\Livewire\DataTableTrait;
use App\Models\Markable\Favorite;
use Livewire\Component;
use Livewire\WithPagination;

class FavouriteAccounts extends Component
{
    use DataTableTrait;
    use WithPagination;

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

        return view('livewire.tables.favourite-accounts', [
            'data' => $this->data,
        ]);
    }

    protected function initQuery()
    {
        $user = auth()->user();
        $this->query = Favorite::where('user_id', $user->id)
            ->whereHasMorph('markable', [
                Account::class,
            ]);
    }
}
