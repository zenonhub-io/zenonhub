<?php

declare(strict_types=1);

namespace App\Domains\Nom\Enums;

enum AcceleratorPhaseStatusEnum: int
{
    case OPEN = 0;
    case PAID = 2;

    public function label(): string
    {
        return match ($this) {
            self::OPEN => 'Open',
            self::PAID => 'Success',
        };
    }

    public function colour(): string
    {
        return match ($this) {
            self::OPEN => 'primary',
            self::PAID => 'success',
        };
    }
}
