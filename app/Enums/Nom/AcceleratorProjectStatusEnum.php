<?php

declare(strict_types=1);

namespace App\Enums\Nom;

enum AcceleratorProjectStatusEnum: int
{
    case NEW = 0;
    case ACCEPTED = 1;
    case REJECTED = 3;
    case COMPLETE = 4;

    public function label(): string
    {
        return match ($this) {
            self::NEW => 'New',
            self::ACCEPTED => 'Accepted',
            self::REJECTED => 'Rejected',
            self::COMPLETE => 'Complete',
        };
    }

    public function colour(): string
    {
        return match ($this) {
            self::NEW => 'light',
            self::ACCEPTED => 'secondary',
            self::REJECTED => 'danger',
            self::COMPLETE => 'primary',
        };
    }
}
