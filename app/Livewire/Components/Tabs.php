<?php

declare(strict_types=1);

namespace App\Livewire\Components;

use Illuminate\Contracts\View\View;
use Livewire\Attributes\On;
use Livewire\Component;

class Tabs extends Component
{
    public array $items = [];

    public string $activeTab;

    public function render(): View
    {
        return view('livewire.components.tabs');
    }

    #[On('tab-changed')]
    public function tabChanged($tab): void
    {
        $this->activeTab = $tab;
        $this->dispatch('show-tab', tab: $tab);
    }
}
