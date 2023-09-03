<?php

namespace App\Http\Livewire\Stats;

use Livewire\Component;

class Bridge extends Component
{
    public string $tab = 'overview';

    protected $queryString = [
        'tab' => ['except' => 'overview'],
    ];

    protected $listeners = ['showTab'];

    public function showTab($tab)
    {
        $this->tab = $tab;
    }

    public function render()
    {
        return view('livewire.stats.bridge');
    }
}
