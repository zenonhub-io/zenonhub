<?php

declare(strict_types=1);

use App\Enums\Nom\NetworkTokensEnum;
use App\Models\Nom\Account;
use App\Models\Nom\Token;
use App\Services\ZenonSdk\ZenonSdk;

function load_account(string $address, ?string $name = null): Account
{
    $account = Account::firstOrCreate([
        'address' => $address,
    ], [
        'chain_id' => app('currentChain')->id,
    ]);

    if ($name) {
        $account->name = $name;
        $account->save();
    }

    return $account;
}

function load_token(?string $zts): ?Token
{
    if (! $zts) {
        return null;
    }

    $token = Token::firstWhere('token_standard', $zts);

    if (! $token) {
        $data = app(ZenonSdk::class)->getByZts($zts);

        $token = Token::firstOrCreate([
            'token_standard' => $data->tokenStandard,
        ], [
            'chain_id' => app('currentChain')->id,
            'owner_id' => load_account($data->owner)->id,
            'name' => $data->name,
            'symbol' => $data->symbol,
            'domain' => $data->domain,
            'max_supply' => $data->maxSupply,
            'decimals' => $data->decimals,
            'is_burnable' => $data->isBurnable,
            'is_mintable' => $data->isMintable,
            'is_utility' => $data->isUtility,
        ]);
    }

    return $token;
}

function lp_eth_token(): Token
{
    return Token::firstWhere('token_standard', NetworkTokensEnum::LP_ZNN_ETH->value);
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

function short_hash($hash, $eitherSide = 8, $includeEnd = true): string
{
    $start = mb_substr($hash, 0, $eitherSide);
    if ($includeEnd) {
        $end = mb_substr($hash, -$eitherSide);

        return "{$start}...{$end}";
    }

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

function hex_to_rgba(string $hex, ?float $alpha = 1): string
{
    $rgb = sscanf($hex, '#%02x%02x%02x');
    $rgb[] = $alpha;

    return implode(',', $rgb);
}
