<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\PageController;
use Auth;
use Illuminate\Http\Request;

class Login extends PageController
{
    public function show()
    {
        if (Auth::check()) {
            return redirect()->route('account.details');
        }

        $this->page['meta']['title'] = 'Login';
        $this->page['data'] = [
            'component' => 'auth.login',
        ];

        return $this->render('pages/auth');
    }

    public function store(Request $request): \Illuminate\Http\RedirectResponse
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

        if (Auth::attempt($credentials, $request->input('remember'))) {
            $request->session()->regenerate();

            $user = auth()->user();
            $user->last_login_at = now();
            auth()->user()->save();

            if ($url = $request->input('redirect')) {
                return redirect()->to($url);
            }

            return redirect()->route('home');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function destroy(Request $request): \Illuminate\Http\RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->back();
    }
}
