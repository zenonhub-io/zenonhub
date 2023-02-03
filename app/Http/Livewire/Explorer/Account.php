<?php

namespace App\Http\Livewire\Explorer;

use Livewire\Component;

class Account extends Component
{
    public string $address;
    public string $tab = 'sent-transactions';
    protected $queryString = [
        'tab' => ['except' => 'sent-transactions']
    ];

    public function render()
    {
        return view('livewire.explorer.account', [
            'account' => \App\Models\Nom\Account::findByAddress($this->address)
        ]);
    }
}
