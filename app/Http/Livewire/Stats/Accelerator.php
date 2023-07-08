<?php

namespace App\Http\Livewire\Stats;

use Livewire\Component;

class Accelerator extends Component
{
    public string $tab = 'funding';

    protected $queryString = [
        'tab' => ['except' => 'funding'],
    ];

    protected $listeners = ['tabChange'];

    public function tabChange($tab = 'funding')
    {
        $this->tab = $tab;
    }

    public function render()
    {
        return view('livewire.stats.accelerator');
    }
}
