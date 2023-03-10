<?php

function znn_token(): \App\Models\Nom\Token
{
    return \App\Models\Nom\Token::findByZts(\App\Models\Nom\Token::ZTS_ZNN);
}

function qsr_token(): \App\Models\Nom\Token
{
    return \App\Models\Nom\Token::findByZts(\App\Models\Nom\Token::ZTS_QSR);
}

function znn_price()
{
    return Cache::get("znn-price");
}

function float_number(mixed $number): float
{
    return floatval(preg_replace('/[^\d.]/', '', $number));
}

function short_number($number): string
{
    $number = float_number($number);

    if ($number < 1 && $number > 0) {
        return '<1';
    }

    $units = ['', 'K', 'M', 'B', 'T'];
    for ($i = 0; $number >= 1000; $i++) {
        $number /= 1000;
    }

    return round($number, 1) . $units[$i];
}

function short_hash($hash, $eitherSide): string
{
    $start = mb_substr($hash, 0, $eitherSide);
    $end = mb_substr($hash, -$eitherSide);
    return "{$start}...{$end}";
}

function pretty_json($json): string
{
    return json_encode($json, JSON_PRETTY_PRINT);
}

function get_env_prefix()
{
    if (! app()->isProduction()) {
        return Str::upper(app()->environment()) . ' - ';
    }

    return null;
}
