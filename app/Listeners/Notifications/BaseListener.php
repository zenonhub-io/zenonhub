<?php

declare(strict_types=1);

namespace App\Listeners\Notifications;

use App\Models\User;
use Illuminate\Contracts\Database\Eloquent\Builder;

class BaseListener
{
    public function getSubscribedUsers(string $notificationCode): Builder
    {
        return User::query()
            ->whereHas('notificationTypes', function ($query) use ($notificationCode) {
                $query->where('code', $notificationCode);
            });
    }
}
