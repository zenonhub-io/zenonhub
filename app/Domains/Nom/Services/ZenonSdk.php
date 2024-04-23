<?php

declare(strict_types=1);

namespace App\Domains\Nom\Services;

use App\Domains\Nom\Services\Sdk\Abi;
use App\Domains\Nom\Services\Sdk\Accelerator;
use App\Domains\Nom\Services\Sdk\Bridge;
use App\Domains\Nom\Services\Sdk\Htlc;
use App\Domains\Nom\Services\Sdk\Ledger;
use App\Domains\Nom\Services\Sdk\Liquidity;
use App\Domains\Nom\Services\Sdk\Pillar;
use App\Domains\Nom\Services\Sdk\Plasma;
use App\Domains\Nom\Services\Sdk\Sentinel;
use App\Domains\Nom\Services\Sdk\Stake;
use App\Domains\Nom\Services\Sdk\Stats;
use App\Domains\Nom\Services\Sdk\Swap;
use App\Domains\Nom\Services\Sdk\Token;
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
