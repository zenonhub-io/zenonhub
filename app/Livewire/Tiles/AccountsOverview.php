<?php

declare(strict_types=1);

namespace App\Livewire\Tiles;

use App\Livewire\BaseComponent;
use App\Models\Nom\Account;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Number;

class AccountsOverview extends BaseComponent
{
    public function render()
    {
        return view('livewire.tiles.accounts-overview', $this->getStats());
    }

    private function getStats(): array
    {
        return Cache::remember('tile.accounts-overview', now()->addMinutes(10), fn () => [
            'total' => Number::abbreviate(Account::count(), 2),
            'dailyActive' => Account::whereDate('last_active_at', now())->count(),
            'dailyCreated' => Account::whereDate('first_active_at', now())->count(),
        ]);
    }
}
