<?php

declare(strict_types=1);

namespace App\Livewire\Tools;

use Illuminate\View\View;
use Livewire\Attributes\Validate;
use Livewire\Component;

class PlasmaBot extends Component
{
    #[Validate([
        'fuseForm.address' => [
            'required',
            'string',
        ],
        'fuseForm.amount' => [
            'required',
            'string',
        ],
    ])]
    public array $fuseForm = [
        'address' => '',
        'amount' => '',
    ];

    public function render(): View
    {
        return view('livewire.tools.plasma-bot');
    }
}
