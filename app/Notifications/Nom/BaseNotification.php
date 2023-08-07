<?php

namespace App\Notifications\Nom;

use App\Models\NotificationType;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class BaseNotification extends Notification
{
    use Queueable;

    protected ?NotificationType $type;

    public function __construct(NotificationType $type = null)
    {
        $this->type = $type;
    }

    public function getType(): ?NotificationType
    {
        return $this->type;
    }
}
