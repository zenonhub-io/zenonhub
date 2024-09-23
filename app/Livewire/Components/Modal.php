<?php

declare(strict_types=1);

namespace App\Livewire\Components;

use Illuminate\Contracts\View\View;
use Livewire\Attributes\On;
use Livewire\Component;

class Modal extends Component
{
    public string $alias;

    public array $params = [];

    public string $activeModal;

    public function render(): View
    {
        return view('livewire.components.modal');
    }

    #[On('open-livewire-modal')]
    public function showModal($alias, $params = []): void
    {
        $this->alias = $alias;
        $this->params = $params;
        $this->activeModal = 'modal-id-' . mt_rand();

        $this->dispatch('show-livewire-modal');
    }

    #[On('reset-livewire-modal')]
    public function resetModal(): void
    {
        $this->reset();
    }
}
