<?php

declare(strict_types=1);

namespace App\Services;

use App\Exceptions\PlasmaBotException;
use App\Services\ZenonCli\ZenonCli;
use Illuminate\Support\Facades\Log;

class PlasmaBot
{
    public function __construct(
        private readonly ZenonCli $cli
    ) {
        $this->cli->setKeystore(config('services.plasma-bot.keystore'));
        $this->cli->setPassphrase(config('services.plasma-bot.passphrase'));
    }

    public function fuse(string $address, int $amount = 10): bool
    {
        try {
            return $this->cli->plasmaFuse($address, $amount);
        } catch (PlasmaBotException $exception) {
            return $this->exceptionHandler($exception);
        }
    }

    public function cancel(string $hash): bool
    {
        try {
            return $this->cli->plasmaCancel($hash);
        } catch (PlasmaBotException $exception) {
            return $this->exceptionHandler($exception);
        }
    }

    public function receiveAll(): bool
    {
        try {
            return $this->cli->receiveAll();
        } catch (PlasmaBotException $exception) {
            return $this->exceptionHandler($exception);
        }
    }

    private function exceptionHandler(PlasmaBotException $exception): bool
    {
        Log::error('Plasma bot error - ' . $exception->getMessage());
        Log::debug($exception);

        return false;
    }
}
