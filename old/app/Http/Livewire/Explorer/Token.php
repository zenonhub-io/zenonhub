<?php

namespace App\Http\Livewire\Explorer;

use Livewire\Component;

class Token extends Component
{
    public string $zts;

    public string $tab = 'holders';

    protected $queryString = [
        'tab' => ['except' => 'holders'],
    ];

    public function render()
    {
        return view('livewire.explorer.token', [
            'token' => \App\Models\Nom\Token::findByZtsWithHolders($this->zts),
        ]);
    }
}
