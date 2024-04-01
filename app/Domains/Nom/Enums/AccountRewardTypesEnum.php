<?php

declare(strict_types=1);

namespace App\Domains\Nom\Enums;

enum AccountRewardTypesEnum: string
{
    case DELEGATE = 'delegate';
    case STAKE = 'stake';
    case PILLAR = 'pillar';
    case SENTINEL = 'sentinel';
    case LIQUIDITY = 'liquidity';
    case LIQUIDITY_PROGRAM = 'liquidity_program';
    case BRIDGE_AFFILIATE = 'bridge_affiliate';

    public function label(): string
    {
        return match ($this) {
            self::DELEGATE => 'Delegate',
            self::STAKE => 'Stake',
            self::PILLAR => 'Pillar',
            self::SENTINEL => 'Sentinel',
            self::LIQUIDITY => 'Liquidity',
            self::LIQUIDITY_PROGRAM => 'Liquidity Program',
            self::BRIDGE_AFFILIATE => 'Bridge Affiliate',
        };
    }
}
