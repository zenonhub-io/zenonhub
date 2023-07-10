<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\PageController;
use App\Models\User;
use Auth;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class Signup extends PageController
{
    public function show()
    {
        if (Auth::check()) {
            return redirect()->route('account.details');
        }

        $this->page['meta']['title'] = 'Sign Up';
        $this->page['data'] = [
            'component' => 'auth.sign-up',
        ];

        return $this->render('pages/auth');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'username' => [
                'required',
                'max:255',
                'alpha_dash',
                Rule::unique(User::class),
            ],
            'email' => [
                'email',
                'max:255',
                Rule::unique(User::class),
            ],
            'password' => ['required', 'confirmed'],
            'terms' => ['required', 'accepted'],
        ]);

        $user = User::create([
            'username' => $request->input('username'),
            'email' => $request->input('email'),
            'password' => $request->input('password'),
            'privacy_confirmed_at' => now(),
            'last_login_at' => now(),
        ]);

        if (! $user) {
            return back()->withErrors([
                'alert' => 'There was an error, please try again',
            ])->exceptInput('password', 'password-confirmation');
        }

        $user->assignRole('user');

        event(new Registered($user));

        Auth::login($user);

        return redirect()->route('verification.notice');
    }
}
