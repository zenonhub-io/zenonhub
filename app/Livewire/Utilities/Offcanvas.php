<?php

namespace App\Livewire\Utilities;

use Illuminate\Contracts\View\View;
use Livewire\Attributes\On;
use Livewire\Component;

class Offcanvas extends Component
{
    public string $alias;

    public string $title;

    public array $params = [];

    public string $activeOffcanvas;

    public function render() : View
    {
        return view('livewire.utilities.offcanvas');
    }

    #[On('open-livewire-offcanvas')]
    public function showOffcanvas($alias, $title, $params = []) : void
    {
        $this->alias = $alias;
        $this->title = $title;
        $this->params = $params;
        $this->activeOffcanvas = 'offcanvas-id-' . mt_rand();

        $this->dispatch('show-livewire-offcanvas');
    }

    #[On('reset-livewire-offcanvas')]
    public function resetOffcanvas() : void
    {
        $this->reset();
    }
}
