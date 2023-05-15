<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\PageController;
use Illuminate\Http\Request;

class SendVerification extends PageController
{
    public function store(Request $request): \Illuminate\Http\RedirectResponse
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
