<?php

namespace App\Http\Livewire\Utilities;

use Livewire\Component;

class Modals extends Component
{
    public $alias;

    public $params = [];

    protected $listeners = ['showModal', 'resetModal'];

    public function render()
    {
        return view('livewire.utilities.modals');
    }

    public function showModal($alias, ...$params)
    {
        $this->alias = $alias;
        $this->params = $params;

        $this->emit('showBootstrapModal');
    }

    public function resetModal()
    {
        $this->reset();
    }
}
