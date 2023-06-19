<?php

namespace App\Http\Livewire\Account;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Livewire\Component;

class Details extends Component
{
    public string $tab = 'details';

    public string $username;

    public string $email;

    public string $old_password;

    public string $new_password;

    public string $new_password_confirmation;

    public ?bool $result;

    protected $queryString = [
        'tab' => ['except' => 'details'],
    ];

    protected $listeners = ['tabChange'];

    public function tabChange($tab = 'details')
    {
        $this->tab = $tab;
        $this->result = null;
    }

    public function mount()
    {
        $this->username = auth()->user()->username;
        $this->email = auth()->user()->email;
    }

    public function render()
    {
        return view('livewire.account.details');
    }

    public function onUpdateDetails(Request $request)
    {
        $validatedData = $this->validate([
            'username' => [
                'required',
                'max:255',
                'alpha_dash',
                Rule::unique(User::class)->ignore($request->user()->id),
            ],
            'email' => [
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($request->user()->id),
            ],
        ]);

        $user = $request->user();
        $user->fill($validatedData);
        $user->save();

        $this->result = true;
    }

    public function onChangePassword(Request $request)
    {
        $validatedData = $this->validate([
            'old_password' => 'current_password',
            'new_password' => 'required|confirmed',
        ]);

        $user = $request->user();
        $user->password = $validatedData['new_password'];
        $user->save();

        $this->result = true;
    }
}
