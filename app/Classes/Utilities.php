<?php

namespace App\Classes;

use App;
use App\Models\Nom\Account;

class Utilities
{
    public static function isPillar(string $account): bool
    {
        $znn = App::make('zenon.api');
        $result = $znn->pillar->getByOwner($account);

        if (! empty($result['data'])) {
            return true;
        }

        return false;
    }

    public static function isSentinel(string $account): bool
    {
        $znn = App::make('zenon.api');
        $result = $znn->sentinel->getByOwner($account);

        if (! empty($result['data'])) {
            return true;
        }

        return false;
    }

    public static function loadAccount(string $address): ?Account
    {
        $account = Account::findByAddress($address);

        if (! $account) {
            $znn = App::make('zenon.api');
            $block = $znn->ledger->getFrontierAccountBlock($address)['data'];
            $account = Account::create([
                'address' => $address,
                'public_key' => $block?->publicKey,
            ]);
        }

        return $account;
    }
}
