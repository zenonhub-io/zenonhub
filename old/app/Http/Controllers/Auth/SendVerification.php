<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SendVerification
{
    public function store(Request $request): RedirectResponse
    {
        if ($request->user()?->hasVerifiedEmail()) {
            return redirect()->route('account.details');
        }

        $request->user()->sendEmailVerificationNotification();

        return redirect()->back()
            ->with('alert', [
                'type' => 'success',
                'message' => 'Verification email sent',
                'icon' => 'check-circle-fill',
            ]);
    }
}
