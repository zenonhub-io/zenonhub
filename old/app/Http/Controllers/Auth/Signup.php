<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Auth;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Meta;

class Signup
{
    public function show()
    {
        if (Auth::check()) {
            return redirect()->route('account.details');
        }

        Meta::title('Sign up to Zenon Hub', false);

        return view('pages/auth', [
            'view' => 'auth.sign-up',
        ]);
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
