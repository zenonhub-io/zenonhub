<?php

declare(strict_types=1);

namespace App\Livewire\Profile;

use App\Actions\Profile\UpdateUserNotificationSubscriptions;
use App\Models\NotificationType;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

// leedsthelightFW.management@nuffieldhealth.com

class ManageNotifications extends Component
{
    public array $notifications = [];

    public function mount(): void
    {
        $user = Auth::user();

        $this->notifications = NotificationType::isActive()
            ->get()
            ->mapWithKeys(fn ($notificationType) => [$notificationType->id => $notificationType->checkUserSubscribed($user)])->toArray();
    }

    public function updateNotificationSubscriptions(UpdateUserNotificationSubscriptions $updater): void
    {
        $this->resetErrorBag();

        $updater->update(Auth::user(), $this->notifications);

        $this->dispatch('profile.notifications.saved');
    }

    public function getUserProperty(): ?Authenticatable
    {
        return Auth::user();
    }

    public function getNotificationTypesProperty($category): Collection
    {
        return NotificationType::isActive()->where('category', $category)->get();
    }

    public function getNotificationCategoriesProperty(): array
    {
        return [
            'zenonhub' => 'Zenon Hub',
            'network' => 'Network',
        ];
    }

    public function render(): View
    {
        return view('livewire.profile.manage-notifications');
    }
}
