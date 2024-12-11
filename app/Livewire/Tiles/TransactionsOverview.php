<?php

declare(strict_types=1);

namespace App\Livewire\Tiles;

use App\Livewire\BaseComponent;
use App\Models\Nom\AccountBlock;
use Illuminate\Support\Number;

class TransactionsOverview extends BaseComponent
{
    public function render()
    {
        return view('livewire.tiles.transactions-overview', [
            'total' => Number::abbreviate(AccountBlock::count(), 2),
            'daily' => Number::abbreviate(AccountBlock::whereDate('created_at', now())->count(), 2),
            'latest' => AccountBlock::latest('id')->first(),
        ]);
    }
}
