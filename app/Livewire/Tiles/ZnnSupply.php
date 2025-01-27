<?php

declare(strict_types=1);

namespace App\Livewire\Tiles;

use App\Livewire\BaseComponent;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Number;

class ZnnSupply extends BaseComponent
{
    public function render(): View
    {
        return view('livewire.tiles.znn-supply', $this->getStats());
    }

    private function getStats(): array
    {
        return Cache::remember('tile.znn-supply', now()->addMinutes(10), function () {
            $znnToken = app('znnToken');

            return [
                'total_supply' => Number::abbreviate($znnToken->getDisplayAmount($znnToken->total_supply), 2),
                'circulating_supply' => Number::abbreviate($znnToken->getDisplayAmount($znnToken->circulating_supply), 2),
                'locked_supply' => Number::abbreviate($znnToken->getDisplayAmount($znnToken->locked_supply), 2),
            ];
        });
    }
}
