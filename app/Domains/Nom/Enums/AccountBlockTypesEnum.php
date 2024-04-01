<?php

declare(strict_types=1);

namespace App\Domains\Nom\Enums;

enum AccountBlockTypesEnum: int
{
    case GENESIS = 1;
    case SEND = 2;
    case RECEIVE = 3;
    case CONTRACT_SEND = 4;
    case CONTRACT_RECEIVE = 5;

    public function label(): string
    {
        return match ($this) {
            self::GENESIS => 'Genesis',
            self::SEND => 'Send',
            self::RECEIVE => 'Receive',
            self::CONTRACT_SEND => 'Contract Send',
            self::CONTRACT_RECEIVE => 'Contract Receive',
        };
    }
}
