<?php

declare(strict_types=1);

namespace App\Actions\Profile;

use App\Models\User;
use App\Traits\PasswordValidationRules;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\ResetsUserPasswords;

class ResetUserPassword implements ResetsUserPasswords
{
    use PasswordValidationRules;

    /**
     * Validate and reset the user's forgotten password.
     *
     * @param  array<string, string>  $input
     */
    public function reset(User $user, array $input): void
    {
        Validator::make($input, [
            'password' => $this->passwordRules(),
        ])->validate();

        $user->forceFill([
            'password' => Hash::make($input['password']),
        ])->save();
    }
}
