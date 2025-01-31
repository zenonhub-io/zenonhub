<?php

declare(strict_types=1);

namespace App\Livewire\Tiles;

use App\Livewire\BaseComponent;
use App\Models\Nom\AccountBlock;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Number;

class TransactionsOverview extends BaseComponent
{
    public function render(): View
    {
        $stats = $this->getStats();
        $stats['latest'] = AccountBlock::latest('id')->first();

        return view('livewire.tiles.transactions-overview', $stats);
    }

    private function getStats(): array
    {
        return Cache::remember('tile.transactions-overview', now()->addMinutes(10), fn () => [
            'total' => Number::abbreviate(AccountBlock::count(), 2),
            'daily' => Number::abbreviate(AccountBlock::whereBetween('created_at', [now()->startOfDay(), now()->endOfDay()])->count(), 2),
        ]);
    }
}
