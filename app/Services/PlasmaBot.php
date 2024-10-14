<?php

declare(strict_types=1);

namespace App\Services;

use App\Exceptions\PlasmaBotException;
use App\Services\ZenonCli\ZenonCli;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;

class PlasmaBot
{
    private ?ZenonCli $cli;

    private ?string $keystore;

    private ?string $passphrase;

    public function __construct()
    {
        $this->keystore = config('plasma-bot.keystore');
        $this->passphrase = config('plasma-bot.passphrase');
        $this->cli = App::make(ZenonCli::class, [
            'node_url' => config('plasma-bot.node_url'),
            'keystore' => $this->keystore,
            'passphrase' => $this->passphrase,
        ]);
    }

    public function install($mnemonic): bool
    {
        try {
            return $this->cli->walletCreateFromMnemonic(
                $mnemonic,
                $this->keystore,
                $this->passphrase
            );
        } catch (PlasmaBotException $exception) {
            return $this->exceptionHandler($exception);
        }
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
