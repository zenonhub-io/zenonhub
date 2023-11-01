<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Meta;

class ForgotPassword
{
    public function show()
    {
        Meta::title('Forgot your password?', false);

        return view('pages/auth', [
            'view' => 'auth.forgot-password',
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate(['email' => 'required|email']);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        return $status === Password::RESET_LINK_SENT
            ? back()->with(['alert.message' => 'We have sent you a password reset link'])
            : back()->withErrors(['email' => __($status)]);
    }
}
