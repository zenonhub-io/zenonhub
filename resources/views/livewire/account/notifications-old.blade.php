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
            <ul class="nav nav-tabs" role="tablist">
                <li class="nav-item">
                    <a href="#general" class="nav-link active" data-bs-toggle="tab" role="tab">
                        General
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#delegate" class="nav-link" data-bs-toggle="tab" role="tab">
                        Delegate
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#pillar" class="nav-link" data-bs-toggle="tab" role="tab">
                        Pillar
                    </a>
                </li>
            </ul>
            <div class="tab-content">
                @php($notificationTypes = \App\Models\NotificationType::isActive()->get())
                @foreach($notificationTypes->pluck('type')->unique()->toArray() as $type)
                    <div class="tab-pane fade {{ ($type === 'general' ? 'show active' : '') }}" id="{{ $type }}" role="tabpanel">
                        <div class="card bg-secondary p-4">
                            @if (
                                $type === 'delegate' && (! auth()->user()->nom_accounts()->count()) ||
                                $type === 'pillar' && (! auth()->user()->is_pillar_owner)
                            )
                                <x-alert
                                    message="You must link a relevant <a href='{{ route('account.addresses') }}' class='fw-bold'>address</a>"
                                    type="info"
                                    icon="exclamation-octagon"
                                    class="mb-0"
                                />
                            @else
                                @foreach ($notificationTypes->where('type', $type)->all() as $notificationType)
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
                                @endforeach
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="mt-4">
                <button class="w-100 btn btn-outline-primary" type="submit">
                    <i class="bi bi-check-circle me-2"></i>
                    Update preferences
                </button>
            </div>
        </form>
    </div>
</div>
