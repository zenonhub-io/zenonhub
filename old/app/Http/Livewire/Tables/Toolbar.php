<?php

namespace App\Http\Livewire\Tables;

use Livewire\Component;

class Toolbar extends Component
{
    public ?string $search;

    public ?bool $enableExport;

    public function render()
    {
        return view('livewire.tables.toolbar');
    }
}
