<?php

namespace App\Services;

use App\Exceptions\ApplicationException;
use Illuminate\Support\Facades\Process;

class PlasmaBot
{
    public function install(): bool
    {
        $keystore = config('plasma-bot.keystore');
        $passphrase = config('plasma-bot.passphrase');
        $mnemonic = config('plasma-bot.mnemonic');
        $result = $this->runCommand("wallet.createFromMnemonic '{$mnemonic}' {$passphrase} {$keystore}");

        if (! $result->seeInOutput('Done')) {
            return false;
        }

        return true;
    }

    public function fuse(string $address, int $amount = 10): bool
    {
        $result = $this->runCommand("plasma.fuse {$address} {$amount}");

        if (! $result->seeInOutput('Done')) {
            return false;
        }

        return true;
    }

    public function cancel(string $hash): bool
    {
        $result = $this->runCommand("plasma.cancel {$hash}");

        if (! $result->seeInOutput('Done')) {
            return false;
        }

        return true;
    }

    private function runCommand(string $action): \Illuminate\Process\ProcessResult
    {
        $path = base_path('bin/znn');
        $flags = collect([
            'u' => config('plasma-bot.node_url'),
            'k' => config('plasma-bot.keystore'),
            'p' => config('plasma-bot.passphrase'),
        ])->implode(fn ($value, $key) => "-{$key} $value ");
        $flags = trim($flags);

        $command = "./znn-cli {$action} {$flags}";
        $result = Process::path($path)->run($command);

        if (! $result->successful()) {
            throw new ApplicationException($result->errorOutput());
        }

        return $result;
    }
}
