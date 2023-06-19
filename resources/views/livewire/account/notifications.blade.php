<div class="card shadow mb-4">
    <div class="card-header">
        <h4 class="mb-0">Your notifications</h4>
    </div>
    <div class="card-body">
        @if($result)
            <x-alert
                message="Notification subscriptions updated"
                type="success"
                icon="check-circle-fill"
            />
        @endif
        <form wire:submit.prevent="onUpdateNotifications" class="needs-validation">
            @foreach ($notificationTypes as $notificationType)
                <div class="mb-3">
                    <div class="form-check form-switch form-switch-success d-flex">
                        <input
                            type="checkbox"
                            id="notification-{{ $notificationType->id }}"
                            class="form-check-input flex-shrink-0"
                            wire:model="notifications.{{ $notificationType->id }}"
                        >
                        <label for="notification-{{ $notificationType->id }}" class="form-check-label ps-3">
                            <span class="h6 d-block mb-2">{{ $notificationType->name }}</span>
                            <span class="fs-sm text-muted">{{ $notificationType->description }}</span>
                        </label>
                    </div>
                </div>
            @endforeach
            <button class="w-100 btn btn-outline-primary" type="submit">
                <i class="bi bi-check-circle me-2"></i>
                Update preferences
            </button>
        </form>
    </div>
</div>
