<?php

namespace App\Http\Controllers\Auth;

use Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Meta;

class Login
{
    public function show()
    {
        if (Auth::check()) {
            return redirect()->route('account.details');
        }

        Meta::title('Login to Zenon Hub', false);

        return view('pages/auth', [
            'view' => 'auth.login',
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
            'redirect' => ['nullable', 'url'],
        ]);

        $credentials = [
            'email' => $request->input('email'),
            'password' => $request->input('password'),
        ];

        if (! Auth::attempt($credentials, $request->input('remember'))) {
            return back()->withErrors([
                'email' => 'The provided credentials do not match our records.',
            ])->onlyInput('email');
        }

        $request->session()->regenerate();

        $user = auth()->user();
        $user->last_login_at = now();
        $user->save();

        if ($url = $request->input('redirect')) {
            return redirect()->to($url);
        }

        return redirect()->route('home');
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->back();
    }
}
