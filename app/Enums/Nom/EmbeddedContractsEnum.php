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
            self::PLASMA => 'Plasma Contract',
            self::PILLAR => 'Pillar Contract',
            self::TOKEN => 'Token Contract',
            self::SENTINEL => 'Sentinel Contract',
            self::SWAP => 'Swap Contract',
            self::STAKE => 'Stake Contract',
            self::SPORK => 'Spork Contract',
            self::ACCELERATOR => 'Accelerator Contract',
            self::LIQUIDITY => 'Liquidity Contract',
            self::BRIDGE => 'Bridge Contract',
            self::HTLC => 'HTLC Contract',
            self::PTLC => 'PTLC Contract',
        };
    }
}
