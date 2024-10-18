<?php

declare(strict_types=1);

namespace App\Services\ZenonCli\Providers;

use App\Exceptions\ZenonCliException;

trait Plasma
{
    public function plasmaFuse(string $address, int $amount = 10): bool
    {
        $result = $this->runCommand("plasma.fuse {$address} {$amount}");

        if (! $result->seeInOutput('Done')) {
            throw new ZenonCliException('Zenon CLI - Unable to fuse');
        }

        return true;
    }

    public function plasmaCancel(string $hash): bool
    {
        $result = $this->runCommand("plasma.cancel {$hash}");

        if (! $result->seeInOutput('Done')) {
            throw new ZenonCliException('Zenon CLI - Unable to cancel');
        }

        return true;
    }
}
