<?php

namespace App\Http\Livewire\Explorer;

use Livewire\Component;

class Momentum extends Component
{
    public string $hash;

    public string $tab = 'transactions';

    protected $queryString = [
        'tab' => ['except' => 'transactions'],
    ];

    protected $listeners = ['momentumChanged' => 'setMomentum'];

    public function render(): \Illuminate\Contracts\View\View
    {
        return view('livewire.explorer.momentum', [
            'momentum' => \App\Models\Nom\Momentum::findByHash($this->hash),
        ]);
    }

    public function setMomentum($hash): void
    {
        $this->hash = $hash;
    }

    public function loadMomentum($hash): void
    {
        $this->hash = $hash;
        $this->emit('urlChanged', route('explorer.momentum', ['hash' => $hash]));
    }
}
