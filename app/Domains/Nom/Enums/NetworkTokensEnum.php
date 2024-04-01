<?php

declare(strict_types=1);

namespace App\Domains\Nom\Enums;

enum NetworkTokensEnum: string
{
    case EMPTY = 'zts1qqqqqqqqqqqqqqqqtq587y';
    case ZNN = 'zts1znnxxxxxxxxxxxxx9z4ulx';
    case QSR = 'zts1qsrxxxxxxxxxxxxxmrhjll';
    case LP_ZNN_ETH = 'zts17d6yr02kh0r9qr566p7tg6';
    case WBTC = 'zts14pmddt35kawqweg3re08zj';

    public function label(): string
    {
        return match ($this) {
            self::EMPTY => 'Empty',
            self::ZNN => 'ZNN',
            self::QSR => 'QSR',
            self::LP_ZNN_ETH => 'wZNN-wETH-LP-ETH',
            self::WBTC => 'WrappedBTC',
        };
    }

    public function symbol(): string
    {
        return match ($this) {
            self::EMPTY => 'Empty',
            self::ZNN => 'ZNN',
            self::QSR => 'QSR',
            self::LP_ZNN_ETH => 'ZNNETHLP',
            self::WBTC => 'WBTC',
        };
    }
}
