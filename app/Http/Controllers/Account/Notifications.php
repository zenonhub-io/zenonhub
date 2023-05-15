<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\PageController;
use App\Models\NotificationType;
use Illuminate\Http\Request;

class Notifications extends PageController
{
    public function show(Request $request)
    {
        $this->page['meta']['title'] = 'Account Notifications';
        $this->page['data'] = [
            'component' => 'account.notifications',
        ];

        return $this->render('pages/account');
    }

    public function store(Request $request)
    {
        $request->validate([
            'notifications' => [
                'array',
            ],
        ]);

        $user = $request->user();
        $existingSubscriptions = $user->notification_types()->pluck('type_id');
        $updatedSubscriptions = [];

        foreach ($request->input('notifications', []) as $typeId => $value) {
            $type = NotificationType::find($typeId);
            if ($type) {
                $updatedSubscriptions[] = $type->id;
                if (! in_array($type->id, $existingSubscriptions->toArray())) {
                    $user->notification_types()->attach($type, [
                        'data' => [],
                    ]);
                } else {
                    $user->notification_types()->updateExistingPivot($type->id, [
                        'data' => [],
                    ]);
                }
            }
        }

        // Unset any other previous subscriptions
        $user->notification_types()->detach(
            $existingSubscriptions->diff($updatedSubscriptions)->toArray()
        );

        return redirect()->route('account.notifications')
            ->with('alert', [
                'type' => 'success',
                'message' => 'Notifications saved!',
                'icon' => 'check-circle-fill',
            ]);
    }
}
