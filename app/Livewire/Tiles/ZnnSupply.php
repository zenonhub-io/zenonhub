<?php

declare(strict_types=1);

namespace App\Livewire\Tiles;

use App\Livewire\BaseComponent;
use Illuminate\Support\Number;

class ZnnSupply extends BaseComponent
{
    public function render()
    {
        $znnToken = app('znnToken');

        return view('livewire.tiles.znn-supply', [
            'total_supply' => Number::abbreviate($znnToken->getDisplayAmount($znnToken->total_supply), 2),
            'circulating_supply' => Number::abbreviate($znnToken->getDisplayAmount($znnToken->circulating_supply), 2),
            'locked_supply' => Number::abbreviate($znnToken->getDisplayAmount($znnToken->locked_supply), 2),
        ]);
    }
}
