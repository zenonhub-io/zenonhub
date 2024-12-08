<?php

declare(strict_types=1);

namespace App\Livewire\Tiles;

use App\Livewire\BaseTile;
use App\Models\Nom\Account;
use Illuminate\Support\Number;

class AccountsOverview extends BaseTile
{
    public function render()
    {
        return view('livewire.tiles.accounts-overview', [
            'total' => Number::abbreviate(Account::count(), 2),
            'dailyActive' => Account::whereDate('last_active_at', now())->count(),
            'dailyCreated' => Account::whereDate('first_active_at', now())->count(),
        ]);
    }
}
