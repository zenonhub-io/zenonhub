<?php

declare(strict_types=1);

namespace App\Services\ZenonCli\Providers;

use App\Exceptions\ZenonCliException;

trait Wallet
{
    public function walletList(): bool
    {
        $result = $this->runCommand('wallet.list');

        if (! $result->seeInOutput('Available wallets')) {
            throw new ZenonCliException('Zenon CLI - Unable to list wallets');
        }

        return true;
    }

    public function walletCreateNew(string $passphrase, string $keystore): bool
    {
        $result = $this->runCommand("wallet.createNew {$passphrase} {$keystore}");

        if (! $result->seeInOutput('Done')) {
            throw new ZenonCliException('Zenon CLI - Unable to create wallet');
        }

        return true;
    }

    public function walletCreateFromMnemonic(string $mnemonic, string $passphrase, string $keystore): bool
    {
        $result = $this->runCommand("wallet.createFromMnemonic '{$mnemonic}' {$passphrase} {$keystore}");

        if (! $result->seeInOutput('Done')) {
            throw new ZenonCliException('Zenon CLI - Unable to create from mnemonic');
        }

        return true;
    }
}
