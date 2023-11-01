<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Meta;

class ResetPassword
{
    public function show($token)
    {
        Meta::title('Reset your password', false);

        return view('pages/auth', [
            'view' => 'auth.reset-password',
            'data' => [
                'token' => $token,
            ],
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|confirmed',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => $password,
                ])->setRememberToken(Str::random(60));

                $user->save();

                event(new PasswordReset($user));
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('login')->with(
                'alert', [
                    'type' => 'success',
                    'message' => 'Your password has been reset',
                    'icon' => 'check-circle-fill',
                ])
            : back()->withErrors(['email' => [__($status)]]);
    }
}
