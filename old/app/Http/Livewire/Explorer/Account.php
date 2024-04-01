<?php

declare(strict_types=1);

namespace App\Http\Livewire\Explorer;

use Livewire\Component;

class Account extends Component
{
    public string $address;

    public string $tab = 'transactions';

    protected $queryString = [
        'tab' => ['except' => 'transactions'],
    ];

    public function render()
    {
        return view('livewire.explorer.account', [
            'account' => \App\Domains\Nom\Models\Account::findBy('address', $this->address),
        ]);
    }
}
