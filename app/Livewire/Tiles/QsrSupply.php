<?php

declare(strict_types=1);

namespace App\Livewire\Tiles;

use App\Livewire\BaseComponent;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Number;

class QsrSupply extends BaseComponent
{
    public function render(): View
    {
        return view('livewire.tiles.qsr-supply', $this->getStats());
    }

    private function getStats(): array
    {
        return Cache::remember('tile.znn-supply', now()->addMinutes(10), function () {
            $qsrToken = app('qsrToken');

            return [
                'total_supply' => Number::abbreviate($qsrToken->getDisplayAmount($qsrToken->total_supply), 2),
                'circulating_supply' => Number::abbreviate($qsrToken->getDisplayAmount($qsrToken->circulating_supply), 2),
                'locked_supply' => Number::abbreviate($qsrToken->getDisplayAmount($qsrToken->locked_supply), 2),
            ];
        });
    }
}
