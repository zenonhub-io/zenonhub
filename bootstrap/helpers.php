<?php

declare(strict_types=1);

use App\Models\Nom\Account;
use App\Models\Nom\Token;
use App\Services\ZenonSdk\ZenonSdk;

if (! function_exists('is_hqz')) {
    function is_hqz(): bool
    {
        return app('currentChain')->code === 'hqz';
    }
}

if (! function_exists('load_account')) {
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
}

if (! function_exists('load_token')) {
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
}

if (! function_exists('short_address')) {
    function short_address(Account $account): string
    {
        if ($account->has_custom_label) {
            return $account->custom_label;
        }

        return short_hash($account->address);
    }
}

if (! function_exists('short_hash')) {
    function short_hash($hash, $eitherSide = 8, $includeEnd = true): string
    {
        $start = mb_substr($hash, 0, $eitherSide);
        if ($includeEnd) {
            $end = mb_substr($hash, -$eitherSide);

            return "{$start}...{$end}";
        }

        return "{$start}...";
    }
}

if (! function_exists('get_env_prefix')) {
    function get_env_prefix(): ?string
    {
        if (! app()->isProduction()) {
            return Str::upper(app()->environment()) . ' - ';
        }

        return null;
    }
}

if (! function_exists('external_url')) {
    function external_url($domain): string
    {
        if (! str_starts_with($domain, 'https://')) {
            $domain = parse_url($domain, PHP_URL_HOST) ?? $domain;
            $domain = "https://{$domain}";
        }

        return $domain;
    }
}

if (! function_exists('app_version_number')) {
    function app_version_number(): string
    {
        return Illuminate\Support\Facades\Cache::rememberForever('system_version_number', function () {
            $composerFile = base_path('composer.json');
            $composer = json_decode(file_get_contents($composerFile), true);

            return $composer['version'];
        });
    }
}

if (! function_exists('progress_bar')) {
    function progress_bar(int $percentage): string
    {
        $empty = '□';
        $full = '■';
        $barTotalLength = 10;

        $fullBars = round($percentage / 10);
        $emptyBars = $barTotalLength - $fullBars;

        return str_repeat($full, max(0, $fullBars)) . str_repeat($empty, max(0, $emptyBars)) . " {$percentage}%";
    }
}

if (! function_exists('hex_to_rgba')) {
    function hex_to_rgba(string $hex, ?float $alpha = 1): string
    {
        $rgb = sscanf($hex, '#%02x%02x%02x');
        $rgb[] = $alpha;

        return implode(',', $rgb);
    }
}
