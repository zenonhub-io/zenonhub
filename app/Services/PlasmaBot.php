<?php

namespace App\Services;

use App\Exceptions\ApplicationException;
use App\Models\PlasmaBotEntry;
use Illuminate\Support\Facades\Process;

class PlasmaBot
{
    public function install(): bool
    {
        $keystore = config('plasma-bot.keystore');
        $passphrase = config('plasma-bot.passphrase');
        $mnemonic = config('plasma-bot.mnemonic');
        $this->runCommand("wallet.createFromMnemonic '{$mnemonic}' {$passphrase} {$keystore}");

        return true;
    }

    public function fuse(string $address, int $amount = 10): bool
    {
        $response = $this->runCommand("plasma.fuse {$address} {$amount}");

        $entry = PlasmaBotEntry::create([
            'address' => $address,
            'amount' => $amount,
            'expires_at' => now()->addDay(),
        ]);

        // TODO - add job to queue to check for confirmation
    }

    public function cancel(string $id): bool
    {
        $response = $this->runCommand("plasma.cancel {$id}");
    }

    private function runCommand(string $action): string
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

        return $result->output();
    }
}
