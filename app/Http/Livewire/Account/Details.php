<?php

namespace App\Http\Livewire\Account;

use Livewire\Component;

class Details extends Component
{
    public string $tab = 'details';

    protected $queryString = [
        'tab' => ['except' => 'details'],
    ];

    protected $listeners = ['tabChange'];

    public function tabChange($tab = 'details')
    {
        $this->tab = $tab;
    }

    public function render()
    {
        return view('livewire.account.details');
    }

    public function onUpdateDetails()
    {

    }

    public function onChangePassword()
    {

    }
}
