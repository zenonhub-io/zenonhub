<?php

declare(strict_types=1);

use App\Domains\Nom\Enums\NetworkTokensEnum;
use App\Domains\Nom\Models\Account;
use App\Domains\Nom\Models\AccountBlock;
use App\Domains\Nom\Models\BridgeAdmin;
use App\Domains\Nom\Models\Chain;
use App\Domains\Nom\Models\Token;
use App\Domains\Nom\Services\ZenonSdk;

function load_chain(): Chain
{
    return Chain::getCurrentChainId();
}

function load_account(string $address, ?string $name = null): Account
{
    $account = Account::findBy('address', $address);

    if (! $account) {
        $account = Account::create([
            'chain_id' => load_chain()->id,
            'address' => $address,
            'name' => $name,
        ]);
    }

    return $account;
}

function load_token(?string $zts): ?Token
{
    if (! $zts) {
        return null;
    }

    $token = Token::findBy('token_standard', $zts);

    if (! $token) {
        $data = app(ZenonSdk::class)->getByZts($zts);
        $token = Token::create([
            'chain_id' => load_chain()->id,
            'owner_id' => load_account($data->owner)->id,
            'name' => $data->name,
            'symbol' => $data->symbol,
            'domain' => $data->domain,
            'token_standard' => $data->tokenStandard,
            'total_supply' => $data->totalSupply,
            'max_supply' => $data->maxSupply,
            'decimals' => $data->decimals,
            'is_burnable' => $data->isBurnable,
            'is_mintable' => $data->isMintable,
            'is_utility' => $data->isUtility,
        ]);
    }

    return $token;
}

function znn_token(): Token
{
    return Token::findBy('token_standard', NetworkTokensEnum::ZNN->value);
}

function qsr_token(): Token
{
    return Token::findBy('token_standard', NetworkTokensEnum::QSR->value);
}

function lp_eth_token(): Token
{
    return Token::findBy('token_standard', NetworkTokensEnum::LP_ZNN_ETH->value);
}

function znn_price(): float
{
    return (float) Cache::get('znn-price');
}

function qsr_price(): float
{
    return (float) Cache::get('qsr-price');
}

function eth_price(): float
{
    return (float) Cache::get('eth-price');
}

function btc_price(): float
{
    return (float) Cache::get('btc-price');
}

function validate_bridge_tx(AccountBlock $block): bool
{
    $bridgeAdmin = BridgeAdmin::getActiveAdmin();

    return $block->account_id === $bridgeAdmin->account_id;
}

function short_address(Account $account)
{
    if ($account->has_custom_label) {
        return $account->custom_label;
    }

    return short_hash($account->address);
}

function float_number(mixed $number): float
{
    return (float) preg_replace('/[^\d.]/', '', $number);
}

function short_hash($hash, $eitherSide = 8, $startAndEnd = true): string
{
    if ($startAndEnd) {
        $start = mb_substr($hash, 0, $eitherSide);
        $end = mb_substr($hash, -$eitherSide);

        return "{$start}...{$end}";
    }

    $start = mb_substr($hash, 0, $eitherSide);

    return "{$start}...";
}

function get_env_prefix(): ?string
{
    if (! app()->isProduction()) {
        return Str::upper(app()->environment()) . ' - ';
    }

    return null;
}

function progress_bar(int $percentage)
{
    $empty = '□';
    $full = '■';
    $barTotalLength = 10;

    $fullBars = round($percentage / 10);
    $emptyBars = $barTotalLength - $fullBars;

    return str_repeat($full, max(0, $fullBars)) . str_repeat($empty, max(0, $emptyBars)) . " {$percentage}%";
}