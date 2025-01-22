<?php

declare(strict_types=1);

namespace App\Actions\PlasmaBot;

use App\Models\User;
use Lorisleiva\Actions\Concerns\AsAction;

class ImportEnvApiKeys
{
    use AsAction;

    public function handle(): void
    {
        $keys = env('PLASMA_BOT_ACCESS_KEYS');
        $keys = explode(',', $keys);
        $user = User::first();

        foreach ($keys as $key) {
            $token = $user->tokens()->create([
                'name' => $key,
                'token' => hash('sha256', $key),
                'abilities' => ['*'],
                'expires_at' => null,
            ]);
        }
    }
}
