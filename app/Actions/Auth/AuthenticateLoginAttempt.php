<?php

declare(strict_types=1);

namespace App\Actions\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthenticateLoginAttempt
{
    public function __invoke(Request $request): ?User
    {
        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return null;
        }

        if (! $user->registration_ip) {
            $user->registration_ip = $request->ip();
        }

        $user->login_ip = $request->ip();
        $user->last_login_at = now();
        $user->save();

        return $user;
    }
}
