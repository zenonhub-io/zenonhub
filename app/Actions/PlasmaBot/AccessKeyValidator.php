<?php

namespace App\Actions\PlasmaBot;

use RuntimeException;

class AccessKeyValidator
{
    public function execute(
        ?string $token,
    ): bool {
        $allowedTokens = config('plasma-bot.access_keys');

        if (! in_array($token, $allowedTokens)) {
            throw new RuntimeException('Invalid access token');
        }

        return true;
    }
}
