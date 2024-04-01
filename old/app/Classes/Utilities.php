<?php

declare(strict_types=1);

namespace App\Classes;

use App\Domains\Nom\Models\Account;
use App\Domains\Nom\Models\AccountBlock;
use App\Domains\Nom\Models\BridgeAdmin;
use App\Domains\Nom\Models\Chain;
use App\Domains\Nom\Models\Token;
use App\Services\ZenonSdk;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;

class Utilities
{
    public static function isPillar(string $account): bool
    {
        $znn = App::make(ZenonSdk::class);
        $result = $znn->pillar->getByOwner($account);

        if (! empty($result['data'])) {
            return true;
        }

        return false;
    }

    public static function isSentinel(string $account): bool
    {
        $znn = App::make(ZenonSdk::class);
        $result = $znn->sentinel->getByOwner($account);

        if (! empty($result['data'])) {
            return true;
        }

        return false;
    }

    public static function loadChain(): Chain
    {
        return Cache::rememberForever('chain', fn () => Chain::getCurrentChainId());
    }

    public static function loadAccount(string $address, ?string $name = null): Account
    {
        $account = Account::findBy('address', $address);

        if (! $account) {
            $znn = App::make(ZenonSdk::class);
            $block = $znn->ledger->getFrontierAccountBlock($address)['data'];
            $chain = self::loadChain();
            $account = Account::create([
                'chain_id' => $chain->id,
                'address' => $address,
                'name' => $name,
                'public_key' => $block?->publicKey,
            ]);
        }

        return $account;
    }

    public static function loadToken(?string $zts): ?Token
    {
        if (! $zts) {
            return null;
        }

        $token = Token::findBy('token_standard', $zts);

        if (! $token) {
            $znn = App::make(ZenonSdk::class);
            $data = $znn->token->getByZts($zts)['data'];
            $chain = self::loadChain();
            $owner = self::loadAccount($data->owner);
            $totalSupply = preg_replace('/[^0-9]/', '', $data->totalSupply);
            $maxSupply = preg_replace('/[^0-9]/', '', $data->maxSupply);

            $token = Token::create([
                'chain_id' => $chain->id,
                'owner_id' => $owner->id,
                'name' => $data->name,
                'symbol' => $data->symbol,
                'domain' => $data->domain,
                'token_standard' => $data->tokenStandard,
                'total_supply' => $totalSupply,
                'max_supply' => $maxSupply,
                'decimals' => $data->decimals,
                'is_burnable' => $data->isBurnable,
                'is_mintable' => $data->isMintable,
                'is_utility' => $data->isUtility,
            ]);
        }

        return $token;
    }

    public static function validateBridgeTx(AccountBlock $block): bool
    {
        $bridgeAdmin = BridgeAdmin::getActiveAdmin();

        return $block->account_id === $bridgeAdmin->account_id;
    }
}
