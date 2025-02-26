<?php

declare(strict_types=1);

namespace App\Enums\Nom;

enum NetworkTokensEnum: string
{
    case EMPTY = 'Empty';
    case ZNN = 'ZNN';
    case QSR = 'QSR';
    case LP_ZNN_ETH = 'ZNNETHLP';
    case WBTC = 'WBTC';

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

    public function zts(): string
    {
        return match ($this) {
            self::EMPTY => 'zts1qqqqqqqqqqqqqqqqtq587y',
            self::ZNN => config('nom.znn_zts'),
            self::QSR => config('nom.qsr_zts'),
            self::LP_ZNN_ETH => 'zts17d6yr02kh0r9qr566p7tg6',
            self::WBTC => 'zts14pmddt35kawqweg3re08zj',
        };
    }
}
