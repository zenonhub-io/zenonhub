<div class="card shadow mb-4">
    <div class="card-header">
        <h4 class="mb-0">Subscribe to notifications</h4>
    </div>
    <div class="card-body">
        @if($result)
            <x-alert
                message="Notification subscriptions updated"
                type="success"
                icon="check-circle-fill"
                class="mb-4"
            />
        @endif
        <form wire:submit.prevent="onUpdateNotifications" class="needs-validation">
            @foreach ($notificationTypes as $notificationType)
                <div class="mb-4">
                    <div class="form-check form-switch form-switch-success d-flex align-items-center">
                        <input
                            type="checkbox"
                            id="notification-{{ $notificationType->id }}"
                            class="form-check-input me-3"
                            wire:model="notifications.{{ $notificationType->id }}"
                        >
                        <label class="form-check-label" for="notification-{{ $notificationType->id }}">
                            <span class="d-block">{{ $notificationType->name }}</span>
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
