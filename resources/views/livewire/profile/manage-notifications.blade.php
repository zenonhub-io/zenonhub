<form wire:submit="updateNotificationSubscriptions">
    <div class="d-flex align-items-end justify-content-between align-items-center">
        <div>
            <h4 class="mb-1">{{ __('Manage your notifications') }}</h4>
            <p class="text-muted">{{ __('Choose which notifications you wish to subscribe to.') }}</p>
        </div>
        <div class="d-none d-md-flex gap-2">
            <x-buttons.button class="btn btn-outline-primary" type="submit" wire:loading.attr="disabled">
                {{ __('Save') }} <i class="bi bi-check-lg ms-2"></i>
            </x-buttons.button>
        </div>
    </div>
    <div class="d-none d-md-block">
        <x-alerts.response class="alert-success my-6" on="profile.notifications.saved">
            <i class="bi bi-check-circle-fill me-2"></i> {{ __('Your notification preferences have been saved') }}
        </x-alerts.response>
    </div>
    <hr class="my-6">
    <div class="vstack gap-6">
        @foreach($this->getNotificationCategoriesProperty() as $categoryKey => $categoryName)
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label mb-0">{{ $categoryName }}</label>
                </div>
                <div class="col-md-10">
                    @if($categoryKey === 'zenonhub')
                        <div class="d-flex mb-5">
                            @php($uuid = Str::random(8))
                            <div class="me-3">
                                <input class="form-check-input"
                                       type="checkbox"
                                       id="{{ $uuid }}"
                                       disabled
                                       checked
                                >
                            </div>
                            <div>
                                <label class="form-label mb-0" for="{{ $uuid }}">
                                    {{ __('Account Updates') }}
                                </label>
                                <p class="text-sm text-muted">
                                    {{ __('Important account & security information') }}
                                </p>
                            </div>
                        </div>
                    @endif
                    @foreach($this->getNotificationTypesProperty($categoryKey) as $notificationType)
                        <div class="d-flex mb-5">
                            @php($uuid = Str::random(8))
                            <div class="me-3">
                                <input class="form-check-input"
                                       type="checkbox"
                                       wire:model="notifications.{{ $notificationType->id }}"
                                       id="{{ $uuid }}"
                                       {{ $notificationType->checkUserSubscribed($this->getUserProperty()) ? 'checked' : null }}
                                >
                            </div>
                            <div>
                                <label class="form-label mb-0" for="{{ $uuid }}">
                                    {{ $notificationType->name }}
                                </label>
                                <p class="text-sm text-muted">
                                    {{ $notificationType->description }}
                                </p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>
    <hr class="my-6 d-md-none">
    <div class="d-md-none">
        <x-alerts.response class="alert-success my-6" on="profile.notifications.saved">
            <i class="bi bi-check-circle-fill me-2"></i> {{ __('Your notification preferences have been saved') }}
        </x-alerts.response>
        <x-buttons.button class="btn btn-outline-primary w-100" type="submit" wire:loading.attr="disabled">
            {{ __('Save') }} <i class="bi bi-check-lg ms-2"></i>
        </x-buttons.button>
    </div>
</form>
