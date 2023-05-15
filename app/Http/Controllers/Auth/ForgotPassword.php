<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\PageController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;

class ForgotPassword extends PageController
{
    public function show()
    {
        $this->page['meta']['title'] = 'Forgot your password?';
        $this->page['data'] = [
            'component' => 'auth.forgot-password',
        ];

        return $this->render('pages/auth');
    }

    public function store(Request $request): \Illuminate\Http\RedirectResponse
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
