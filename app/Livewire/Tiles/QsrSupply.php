<?php

declare(strict_types=1);

namespace App\Livewire\Tiles;

use App\Livewire\BaseComponent;
use Illuminate\Support\Number;

class QsrSupply extends BaseComponent
{
    public function render()
    {
        $qsrToken = app('qsrToken');

        return view('livewire.tiles.qsr-supply', [
            'total_supply' => Number::abbreviate($qsrToken->getDisplayAmount($qsrToken->total_supply), 2),
            'circulating_supply' => Number::abbreviate($qsrToken->getDisplayAmount($qsrToken->circulating_supply), 2),
            'locked_supply' => Number::abbreviate($qsrToken->getDisplayAmount($qsrToken->locked_supply), 2),
        ]);
    }
}
