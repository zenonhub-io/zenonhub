<?php

declare(strict_types=1);

namespace App\Http\Livewire\Account;

use App\Models\NotificationType;
use Illuminate\Http\Request;
use Livewire\Component;

class Notifications extends Component
{
    public string $tab = 'general';

    public array $notifications = [];

    public ?bool $result;

    protected $queryString = [
        'tab' => ['except' => 'general'],
    ];

    protected $listeners = ['tabChange'];

    public function tabChange($tab = 'general')
    {
        $this->tab = $tab;
    }

    public function mount()
    {
        $types = NotificationType::whereActive()->get();
        $userSubscriptions = auth()
            ->user()
            ->notification_types()
            ->pluck('type_id')
            ->toArray();

        $this->notifications = $types->mapWithKeys(fn ($type) => [$type->id => in_array($type->id, $userSubscriptions)])->toArray();
    }

    public function render()
    {
        return view('livewire.account.notifications', [
            'notificationTabs' => NotificationType::getAllCategories(),
            'notificationTypes' => NotificationType::whereActive()->get(),
        ]);
    }

    public function onUpdateNotifications(Request $request)
    {
        $validatedData = $this->validate([
            'notifications' => [
                'array',
            ],
        ]);

        $user = $request->user();
        $existingSubscriptions = $user->notification_types()
            ->pluck('type_id')
            ->toArray();

        foreach ($validatedData['notifications'] as $typeId => $subscribe) {
            $type = NotificationType::find($typeId);

            if (! $type) {
                continue;
            }

            if ($subscribe) {
                if (! in_array($type->id, $existingSubscriptions)) {
                    $user->notification_types()->attach($type, [
                        'data' => [],
                    ]);
                } else {
                    $user->notification_types()->updateExistingPivot($type->id, [
                        'data' => [],
                    ]);
                }
            } else {
                $user->notification_types()->detach($typeId);
            }
        }

        $this->result = true;
    }
}
