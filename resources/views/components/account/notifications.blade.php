<div class="card shadow mb-4">
    <div class="card-header">
        <h4 class="mb-0">Your notifications</h4>
    </div>
    <div class="card-body">
        @if (session('alert'))
            <x-alert
                message="{{ session('alert.message') }}"
                type="{{ session('alert.type') }}"
                icon="{{ session('alert.icon') }}"
                class="d-flex align-items-center"
            />
        @endif
        <form action="{{ route('account.notifications') }}" method="post" class="needs-validation">
            @csrf
            @php($notificationTypes = \App\Models\NotificationType::isActive()->where('type', 'general')->get())
            @foreach ($notificationTypes as $notificationType)
                <div class="mb-3">
                    <div class="form-check form-switch form-switch-success d-flex">
                        <input
                            type="checkbox"
                            id="notification-{{ $notificationType->id }}"
                            class="form-check-input flex-shrink-0"
                            name="notifications[{{ $notificationType->id }}]"
                            @if (auth()->user()->notification_types()->where('type_id', $notificationType->id)->exists())
                                checked=""
                            @endif
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
