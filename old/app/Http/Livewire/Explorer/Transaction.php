<?php

declare(strict_types=1);

namespace App\Http\Livewire\Explorer;

use Livewire\Component;

class Transaction extends Component
{
    public string $hash;

    public string $tab = 'descendants';

    protected $queryString = [
        'tab' => ['except' => 'descendants'],
    ];

    protected $listeners = ['transactionChanged' => 'setTransaction'];

    public function render(): \Illuminate\Contracts\View\View
    {
        return view('livewire.explorer.transaction', [
            'transaction' => \App\Domains\Nom\Models\AccountBlock::firstWhere('hash', $this->hash),
        ]);
    }

    public function setTransaction($hash): void
    {
        $this->hash = $hash;
    }

    public function loadTransaction($hash): void
    {
        $this->hash = $hash;
        $this->emit('urlChanged', route('explorer.transaction', ['hash' => $hash]));
    }
}
