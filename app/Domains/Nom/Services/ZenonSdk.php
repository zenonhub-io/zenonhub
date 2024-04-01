<?php

declare(strict_types=1);

namespace App\Domains\Nom\Services;

use App\Domains\Nom\Models\Account;
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
    use Accelerator, Bridge, Htlc, Ledger, Liquidity, Pillar, Plasma, Sentinel, Stake, Stats, Swap, Token;

    public function __construct(
        protected Zenon $sdk
    ) {
    }

    public function verifySignature(string $publicKey, string $address, string $message, string $signature): bool
    {
        $validated = false;
        $validSignature = Utilities::verifySignedMessage(
            $publicKey,
            $message,
            $signature
        );

        $account = Account::findBy('address', $address);
        $accountCheck = Utilities::addressFromPublicKey($publicKey);

        if ($validSignature && ($address === $accountCheck)) {
            $validated = true;
        }

        if ($validated && $account && ! $account->public_key) {
            $account->public_key = base64_encode(Utilities::hexToBin($publicKey));
            $account->save();
        }

        return $validated;
    }
}
