<?php

declare(strict_types=1);

namespace App\Domains\Nom\Enums;

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
            self::ACCEPTED => 'primary',
            self::REJECTED => 'danger',
            self::COMPLETE => 'success',
        };
    }
}
