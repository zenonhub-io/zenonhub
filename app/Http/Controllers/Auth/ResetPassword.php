<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\PageController;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class ResetPassword extends PageController
{
    public function show($token)
    {
        $this->page['meta']['title'] = 'Reset your password';
        $this->page['data'] = [
            'component' => 'auth.reset-password',
            'token' => $token
        ];

        return $this->render('pages/auth');
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request): \Illuminate\Http\RedirectResponse
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
                    'password' => $password
                ])->setRememberToken(Str::random(60));

                $user->save();

                event(new PasswordReset($user));
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('login')->with(
                'alert' , [
                'type' => 'success',
                'message' => 'Your password has been reset',
                'icon' => 'check-circle-fill',
            ])
            : back()->withErrors(['email' => [__($status)]]);
    }
}
