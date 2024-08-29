<?php

declare(strict_types=1);

namespace App\Enums\Nom;

enum EmbeddedContractsEnum: string
{
    case PLASMA = 'z1qxemdeddedxplasmaxxxxxxxxxxxxxxxxsctrp';
    case PILLAR = 'z1qxemdeddedxpyllarxxxxxxxxxxxxxxxsy3fmg';
    case TOKEN = 'z1qxemdeddedxt0kenxxxxxxxxxxxxxxxxh9amk0';
    case SENTINEL = 'z1qxemdeddedxsentynelxxxxxxxxxxxxxwy0r2r';
    case SWAP = 'z1qxemdeddedxswapxxxxxxxxxxxxxxxxxxl4yww';
    case STAKE = 'z1qxemdeddedxstakexxxxxxxxxxxxxxxxjv8v62';
    case SPORK = 'z1qxemdeddedxsp0rkxxxxxxxxxxxxxxxx956u48';
    case ACCELERATOR = 'z1qxemdeddedxaccelerat0rxxxxxxxxxxp4tk22';
    case LIQUIDITY = 'z1qxemdeddedxlyquydytyxxxxxxxxxxxxflaaae';
    case BRIDGE = 'z1qxemdeddedxdrydgexxxxxxxxxxxxxxxmqgr0d';
    case HTLC = 'z1qxemdeddedxhtlcxxxxxxxxxxxxxxxxxygecvw';
    case PTLC = 'z1qxemdeddedxptlcxxxxxxxxxxxxxxxxx6lqady';

    public function label(): string
    {
        return match ($this) {
            self::PLASMA => 'Plasma contract',
            self::PILLAR => 'Pillar contract',
            self::TOKEN => 'Token contract',
            self::SENTINEL => 'Sentinel contract',
            self::SWAP => 'Swap contract',
            self::STAKE => 'Stake contract',
            self::SPORK => 'Spork contract',
            self::ACCELERATOR => 'Accelerator contract',
            self::LIQUIDITY => 'Liquidity contract',
            self::BRIDGE => 'Bridge contract',
            self::HTLC => 'HTLC contract',
            self::PTLC => 'PTLC contract',
        };
    }
}
