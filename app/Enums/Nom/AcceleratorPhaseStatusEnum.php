<?php

declare(strict_types=1);

namespace App\Enums\Nom;

enum AcceleratorPhaseStatusEnum: int
{
    case OPEN = 0;
    case PAID = 2;

    public function label(): string
    {
        return match ($this) {
            self::OPEN => 'Open',
            self::PAID => 'Paid',
        };
    }

    public function colour(): string
    {
        return match ($this) {
            self::OPEN => 'secondary',
            self::PAID => 'primary',
        };
    }
}
