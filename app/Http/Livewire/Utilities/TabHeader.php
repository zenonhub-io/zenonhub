<?php

namespace App\Http\Livewire\Utilities;

use Livewire\Component;

class TabHeader extends Component
{
    public $tabs = [];

    public $activeTab;

    protected $listeners = ['tabChanged'];

    public function render()
    {
        return view('livewire.utilities.tab-header');
    }

    public function tabChanged($tab)
    {
        $this->activeTab = $tab;
        $this->emit('showTab', $tab);
    }
}
