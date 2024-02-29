<form wire:submit="updateProfileInformation">
    <div class="d-flex align-items-end justify-content-between align-items-center">
        <div>
            <h4 class="mb-1">{{ __('Update your details') }}</h4>
            <p class="text-muted">{{ __('Update your account\'s profile information and email address.') }}</p>
        </div>
        <div class="d-none d-md-flex gap-2">
            <x-buttons.button class="btn btn-outline-primary" type="submit" wire:loading.attr="disabled">
                {{ __('Save') }} <i class="bi bi-check-lg ms-2"></i>
            </x-buttons.button>
        </div>
    </div>
    <div class="d-none d-md-block">
        <x-alerts.response class="alert-success my-6" on="profile.details.saved">
            <i class="bi bi-check-circle-fill me-2"></i> {{ __('Your profile details have been updated') }}
        </x-alerts.response>
    </div>
    <hr class="my-6">
    <div class="vstack gap-6">
        <div class="row align-items-center">
            @php($uuid = Str::random(8))
            <div class="col-md-4">
                <x-forms.label :label="__('Name')" for="{{ $uuid }}" />
            </div>
            <div class="col-md-12">
                <x-forms.inputs.input name="name" id="{{ $uuid }}" wire:model="state.name" autocomplete="name" />
            </div>
        </div>
        <div class="row align-items-center">
            @php($uuid = Str::random(8))
            <div class="col-md-4">
                <x-forms.label :label="__('Username')" for="{{ $uuid }}" />
            </div>
            <div class="col-md-12">
                <x-forms.inputs.input name="username" id="{{ $uuid }}" wire:model="state.username" autocomplete="username" required />
            </div>
        </div>
        <div class="row align-items-center">
            @php($uuid = Str::random(8))
            <div class="col-md-4">
                <x-forms.label :label="__('Email')" for="{{ $uuid }}" />
            </div>
            <div class="col-md-12">
                <x-forms.inputs.email name="email" id="{{ $uuid }}" wire:model="state.email" autocomplete="email" required />
            </div>
            @if (! $this->user->hasVerifiedEmail())

                <div class="col-md-16">
                    <x-alerts.alert type="warning" class="mt-4 d-flex align-items-center justify-content-between">
                        <span>
                            <i class="bi bi-exclamation-circle-fill me-2"></i> {{ __('Your email address is unverified.') }}
                        </span>
                        <button type="button" class="btn btn-outline-warning btn-sm text-nowrap" wire:click.prevent="sendEmailVerification">
                            {{ __('Re-send verification') }} <i class="bi bi-send-fill ms-2"></i>
                        </button>
                    </x-alerts.alert>
                </div>

                @if ($this->verificationLinkSent)
                    <p class="mt-2 text-success">
                        {{ __('A new verification link has been sent to your email address.') }}
                    </p>
                @endif
            @endif
        </div>
    </div>
    <hr class="my-6 d-md-none">
    <div class="d-md-none">
        <x-alerts.response class="alert-success my-6" on="profile.details.saved">
            <i class="bi bi-check-circle-fill me-2"></i> {{ __('Your profile details have been updated') }}
        </x-alerts.response>
        <x-buttons.button class="btn btn-outline-primary w-100" type="submit" wire:loading.attr="disabled">
            {{ __('Save') }} <i class="bi bi-check-lg ms-2"></i>
        </x-buttons.button>
    </div>
</form>
