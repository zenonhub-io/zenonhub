<?php

namespace App\Services;

use App;

class PlasmaBot
{
    private ?ZnnCli $cli;

    private ?string $keystore;

    private ?string $passphrase;

    public function __construct()
    {
        $this->keystore = config('plasma-bot.keystore');
        $this->passphrase = config('plasma-bot.passphrase');
        $this->cli = App::make(ZnnCli::class, [
            'node_url' => config('plasma-bot.node_url'),
            'keystore' => $this->keystore,
            'passphrase' => $this->passphrase,
        ]);
    }

    public function install(): bool
    {
        return $this->cli->walletCreateFromMnemonic(
            config('plasma-bot.mnemonic'),
            $this->keystore,
            $this->passphrase
        );
    }

    public function fuse(string $address, int $amount = 10): bool
    {
        return $this->cli->plasmaFuse($address, $amount);
    }

    public function cancel(string $hash): bool
    {
        return $this->cli->plasmaCancel($hash);
    }

    public function receiveAll(): bool
    {
        return $this->cli->receiveAll();
    }
}
