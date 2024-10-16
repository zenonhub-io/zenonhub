<?php

declare(strict_types=1);

namespace App\Services\ZenonSdk;

use App\Services\ZenonSdk\Providers\Accelerator;
use App\Services\ZenonSdk\Providers\Bridge;
use App\Services\ZenonSdk\Providers\Htlc;
use App\Services\ZenonSdk\Providers\Ledger;
use App\Services\ZenonSdk\Providers\Liquidity;
use App\Services\ZenonSdk\Providers\Pillar;
use App\Services\ZenonSdk\Providers\Plasma;
use App\Services\ZenonSdk\Providers\Sentinel;
use App\Services\ZenonSdk\Providers\Stake;
use App\Services\ZenonSdk\Providers\Stats;
use App\Services\ZenonSdk\Providers\Swap;
use App\Services\ZenonSdk\Providers\Token;
use DigitalSloth\ZnnPhp\Utilities;
use DigitalSloth\ZnnPhp\Zenon;
use Throwable;

class ZenonSdk
{
    use Abi, Accelerator, Bridge, Htlc, Ledger, Liquidity, Pillar, Plasma, Sentinel, Stake, Stats, Swap, Token;

    public function __construct(
        protected Zenon $sdk
    ) {}

    public function verifySignature(string $publicKey, string $address, string $message, string $signature): bool
    {
        $validSignature = Utilities::verifySignedMessage(
            $publicKey,
            $message,
            $signature
        );

        $accountCheck = Utilities::addressFromPublicKey($publicKey);

        return $validSignature && ($address === $accountCheck);
    }

    public function addressFromPublicKey(string $publicKey): ?string
    {
        try {
            return Utilities::addressFromPublicKey($publicKey);
        } catch (Throwable $throwable) {
            return null;
        }
    }

    public function ztsFromHash(string $hash): ?string
    {
        try {
            return Utilities::ztsFromHash($hash);
        } catch (Throwable $throwable) {
            return null;
        }
    }
}
