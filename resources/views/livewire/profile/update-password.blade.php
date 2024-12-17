<form wire:submit="updatePassword">
    <div class="d-flex align-items-end justify-content-between align-items-center">
        <div>
            <h4 class="mb-1">{{ __('Change your password') }}</h4>
            <p class="text-muted">{{ __('Ensure your account is using a long, random password to stay secure.') }}</p>
        </div>
        <div class="d-none d-md-flex gap-2">
            <x-buttons.button class="btn btn-outline-primary" type="submit" wire:loading.attr="disabled">
                {{ __('Save') }} <i class="bi bi-check-lg ms-2"></i>
            </x-buttons.button>
        </div>
    </div>
    <div class="d-none d-md-block">
        <x-alerts.response class="alert-success my-6" on="profile.password.saved">
            <i class="bi bi-check-circle-fill me-2"></i> {{ __('Your password has been updated') }}
        </x-alerts.response>
    </div>
    <hr class="my-6">
    <div class="vstack gap-6">
        <div class="row align-items-center">
            @php($uuid = Str::random(8))
            <div class="col-md-4">
                <x-forms.label :label="__('Current Password')" for="{{ $uuid }}" />
            </div>
            <div class="col-md-12">
                <x-forms.inputs.password name="current_password" id="{{ $uuid }}" autocomplete="current-password" wire:model="state.current_password" required />
            </div>
        </div>
        <div class="row align-items-center">
            @php($uuid = Str::random(8))
            <div class="col-md-4">
                <x-forms.label :label="__('New Password')" for="{{ $uuid }}" />
            </div>
            <div class="col-md-12">
                <x-forms.inputs.password name="password" id="{{ $uuid }}" autocomplete="new-password" wire:model="state.password" required />
            </div>
        </div>
        <div class="row align-items-center">
            @php($uuid = Str::random(8))
            <div class="col-md-4">
                <x-forms.label :label="__('Confirm Password')" for="{{ $uuid }}" />
            </div>
            <div class="col-md-12">
                <x-forms.inputs.password name="password_confirmation" id="{{ $uuid }}" autocomplete="new-password" wire:model="state.password_confirmation" required />
            </div>
        </div>
    </div>
    <hr class="my-6 d-md-none">
    <div class="d-md-none">
        <x-alerts.response class="alert-success my-6" on="profile.password.saved">
            <i class="bi bi-check-circle-fill me-2"></i> {{ __('Your password has been updated') }}
        </x-alerts.response>
        <x-buttons.button class="btn btn-outline-primary w-100" type="submit" wire:loading.attr="disabled">
            {{ __('Save') }} <i class="bi bi-check-lg ms-2"></i>
        </x-buttons.button>
    </div>
</form>
