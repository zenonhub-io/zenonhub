<?php

namespace App\Services;

use App\Models\Nom\Account;
use DigitalSloth\ZnnPhp\Utilities;
use DigitalSloth\ZnnPhp\Zenon;

class ZenonSdk
{
    protected Zenon $zenonSdk;

    public function __construct(
        private readonly ?string $node
    ) {
        $this->zenonSdk = new Zenon($this->node, config('zenon.throw_api_errors'));
    }

    public function getSdk(): Zenon
    {
        return $this->zenonSdk;
    }

    public static function verifySignature(string $publicKey, string $address, string $message, string $signature): bool
    {
        $validated = false;
        $validSignature = Utilities::verifySignedMessage(
            $publicKey,
            $message,
            $signature
        );

        $account = Account::findByAddress($address);
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
