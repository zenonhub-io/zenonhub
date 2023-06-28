<?php

namespace App\Http\Livewire\Account;

use Livewire\Component;

class Favorites extends Component
{
    public string $tab = 'addresses';

    protected $queryString = [
        'tab' => ['except' => 'addresses'],
    ];

    protected $listeners = ['tabChange'];

    public function tabChange($tab = 'addresses')
    {
        $this->tab = $tab;
    }

    public function render()
    {
        return view('livewire.account.favorites');
    }
}
