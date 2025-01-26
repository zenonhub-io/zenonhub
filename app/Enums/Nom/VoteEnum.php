<?php

declare(strict_types=1);

namespace App\Enums\Nom;

enum VoteEnum: int
{
    case YES = 0;
    case NO = 1;
    case ABSTAIN = 2;

    public function label(): string
    {
        return match ($this) {
            self::YES => 'Yes',
            self::NO => 'No',
            self::ABSTAIN => 'Abstain',
        };
    }

    public function colour(): string
    {
        return match ($this) {
            self::YES => 'success',
            self::NO => 'danger',
            self::ABSTAIN => 'warning',
        };
    }
}
