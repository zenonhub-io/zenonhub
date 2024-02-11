<?php

namespace App\Http\Controllers\Auth;

use Auth;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Meta;

class VerifyEmail
{
    public function show()
    {
        if (Auth::check() && Auth::user()->email_verified_at) {
            return redirect()->route('account.details');
        }

        Meta::title('Verify your email');

        return view('pages/auth', [
            'view' => 'auth.verify-email',
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        if (! hash_equals((string) $request->route('id'), (string) $request->user()->getKey())) {
            throw new AuthorizationException;
        }

        if (! hash_equals((string) $request->route('hash'), sha1($request->user()->getEmailForVerification()))) {
            throw new AuthorizationException;
        }

        if (! $request->user()->hasVerifiedEmail()) {
            $request->user()->markEmailAsVerified();

            event(new Verified($request->user()));
        }

        return redirect()->route('account.details')
            ->with('alert', [
                'type' => 'success',
                'message' => 'Email address verified',
                'icon' => 'check-circle-fill',
            ]);
    }
}
