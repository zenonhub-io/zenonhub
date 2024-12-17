<?php

declare(strict_types=1);

namespace App\Traits;

use Illuminate\Validation\Rules\Password;

trait PasswordValidationRules
{
    protected function passwordRules(): array
    {
        if (app()->isProduction()) {
            return ['required', 'string', new Password(8), 'confirmed'];
        }

        return ['required', 'string', new Password(3), 'confirmed'];
    }
}
