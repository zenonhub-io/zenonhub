<?php

namespace App\Actions\Profile;

use App\Models\User;

class UpdateUserNotificationSubscriptions
{
    public function update(
        User $user,
        array $subscriptions
    ) : void {
        $existingSubscriptions = $user->notificationTypes
            ->pluck('id');

        foreach ($subscriptions as $subscriptionId => $isSubscribed) {
            if ($isSubscribed) {
                if (! $existingSubscriptions->contains($subscriptionId)) {
                    $user->notificationTypes()->attach($subscriptionId, [
                        'data' => [],
                    ]);
                } else {
                    $user->notificationTypes()->updateExistingPivot($subscriptionId, [
                        'data' => [],
                    ]);
                }
            } else {
                $user->notificationTypes()->detach($subscriptionId);
            }
        }
    }
}
