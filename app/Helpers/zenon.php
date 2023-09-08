<?php

function znn_token(): App\Models\Nom\Token
{
    return \App\Models\Nom\Token::findByZts(\App\Models\Nom\Token::ZTS_ZNN);
}

function qsr_token(): App\Models\Nom\Token
{
    return \App\Models\Nom\Token::findByZts(\App\Models\Nom\Token::ZTS_QSR);
}

function lp_eth_token(): App\Models\Nom\Token
{
    return \App\Models\Nom\Token::findByZts(\App\Models\Nom\Token::ZTS_LP_ETH);
}

function znn_price(): float
{
    return (float) Cache::get('znn-price');
}

function qsr_price(): float
{
    return (float) Cache::get('qsr-price');
}

function float_number(mixed $number): float
{
    return (float) preg_replace('/[^\d.]/', '', $number);
}

function short_number($number): string
{
    if ($number < 1 && $number > 0) {
        return $number;
    }

    $number = float_number($number);

    $units = ['', 'K', 'M', 'B', 'T'];
    for ($i = 0; $number >= 1000; $i++) {
        $number /= 1000;
    }

    return round($number, 1).$units[$i];
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

function pretty_json($json): string
{
    return json_encode($json, JSON_PRETTY_PRINT);
}

function get_env_prefix(): ?string
{
    if (! app()->isProduction()) {
        return Str::upper(app()->environment()).' - ';
    }

    return null;
}
