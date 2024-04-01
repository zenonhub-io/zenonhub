<?php

declare(strict_types=1);

namespace App\Domains\Nom\Services;

use App\Exceptions\PlasmaBotException;
use Illuminate\Process\ProcessResult;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Process;

class ZenonCli
{
    public function __construct(
        protected string $nodeUrl,
        protected ?string $keystore = null,
        protected ?string $passphrase = null,
    ) {
    }

    //
    // Wallet

    public function walletCreateNew(string $passphrase, string $keystore): bool
    {
        $result = $this->runCommand("wallet.createNew {$passphrase} {$keystore}");

        if (! $result->seeInOutput('Done')) {
            return false;
        }

        return true;
    }

    public function walletCreateFromMnemonic(string $mnemonic, string $passphrase, string $keystore): bool
    {
        $result = $this->runCommand("wallet.createFromMnemonic '{$mnemonic}' {$passphrase} {$keystore}");

        if (! $result->seeInOutput('Done')) {
            return false;
        }

        return true;
    }

    //
    // General

    public function send(string $toAddress, int $amount): bool
    {
        $result = $this->runCommand("send {$toAddress} {$amount}");

        if (! $result->seeInOutput('Done')) {
            return false;
        }

        return true;
    }

    public function receive(string $hash): bool
    {
        $result = $this->runCommand("receive {$hash}");

        if (! $result->seeInOutput('Done')) {
            return false;
        }

        return true;
    }

    public function receiveAll(): bool
    {
        $result = $this->runCommand('receiveAll');

        if (! $result->seeInOutput('Done')) {
            return false;
        }

        return true;
    }

    //
    // Plasma

    public function plasmaFuse(string $address, int $amount = 10): bool
    {
        $result = $this->runCommand("plasma.fuse {$address} {$amount}");

        if (! $result->seeInOutput('Done')) {
            return false;
        }

        return true;
    }

    public function plasmaCancel(string $hash): bool
    {
        $result = $this->runCommand("plasma.cancel {$hash}");

        if (! $result->seeInOutput('Done')) {
            return false;
        }

        return true;
    }

    //
    // Internal

    /**
     * @throws PlasmaBotException
     */
    private function runCommand(string $action): ProcessResult
    {
        $path = base_path('bin/znn');
        $flags = collect([
            'u' => $this->nodeUrl,
            'k' => $this->keystore,
            'p' => $this->passphrase,
        ])->filter()->implode(fn ($value, $key) => "-{$key} $value ");
        $flags = trim($flags);

        $command = "./znn-cli {$action} {$flags}";
        $result = Process::path($path)->run($command);

        if (! $result->successful()) {
            Log::error($result->errorOutput());
            throw new PlasmaBotException($result->errorOutput());
        }

        return $result;
    }
}
