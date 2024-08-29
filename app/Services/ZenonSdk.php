<?php

declare(strict_types=1);

namespace App\Services;

use App\Services\ZenonSdk\Abi;
use App\Services\ZenonSdk\Accelerator;
use App\Services\ZenonSdk\Bridge;
use App\Services\ZenonSdk\Htlc;
use App\Services\ZenonSdk\Ledger;
use App\Services\ZenonSdk\Liquidity;
use App\Services\ZenonSdk\Pillar;
use App\Services\ZenonSdk\Plasma;
use App\Services\ZenonSdk\Sentinel;
use App\Services\ZenonSdk\Stake;
use App\Services\ZenonSdk\Stats;
use App\Services\ZenonSdk\Swap;
use App\Services\ZenonSdk\Token;
use DigitalSloth\ZnnPhp\Utilities;
use DigitalSloth\ZnnPhp\Zenon;

class ZenonSdk
{
    use Abi, Accelerator, Bridge, Htlc, Ledger, Liquidity, Pillar, Plasma, Sentinel, Stake, Stats, Swap, Token;

    public function __construct(
        protected Zenon $sdk
    ) {
    }

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
}
