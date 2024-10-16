<?php

declare(strict_types=1);

namespace App\Services\ZenonCli\Providers;

trait Wallet
{
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
}
