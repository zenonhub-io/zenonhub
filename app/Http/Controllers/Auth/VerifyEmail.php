<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\PageController;
use Auth;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\Request;

class VerifyEmail extends PageController
{
    public function show()
    {
        if (Auth::check() && Auth::user()->email_verified_at) {
            return redirect()->route('account.details');
        }

        $this->page['meta']['title'] = 'Verify your email';
        $this->page['data'] = [
            'component' => 'auth.verify-email',
        ];

        return $this->render('pages/auth');
    }

    /**
     * @throws AuthorizationException
     */
    public function store(Request $request): \Illuminate\Http\RedirectResponse
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
