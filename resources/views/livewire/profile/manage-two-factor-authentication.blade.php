<div>
    <div class="d-flex align-items-end justify-content-between align-items-center">
        <div class="me-3">
            <h4 class="mb-1">{{ __('Two Factor Authentication') }}</h4>
            <p class="text-muted">{{ __('Add additional security to your account using two factor authentication.') }}</p>
        </div>
        <div class="d-none d-md-flex ms-auto gap-4 text-nowrap">
            @if (! $this->enabled)
                <x-utilities.confirm-password wire:then="enableTwoFactorAuthentication" class="btn-outline-primary" wire:loading.attr="disabled">
                    {{ __('Enable') }} <i class="bi bi-lock-fill ms-2"></i>
                </x-utilities.confirm-password>
            @else
                @if ($showingRecoveryCodes)
                    <x-utilities.confirm-password wire:then="regenerateRecoveryCodes" class="btn-neutral" wire:loading.attr="disabled">
                        {{ __('Regenerate Codes') }} <i class="bi bi-arrow-clockwise ms-2"></i>
                    </x-utilities.confirm-password>
                @elseif ($showingConfirmation)
                    <x-utilities.confirm-password wire:then="confirmTwoFactorAuthentication" class="btn-outline-primary" wire:loading.attr="disabled">
                        {{ __('Confirm') }} <i class="bi bi-check-lg ms-2"></i>
                    </x-utilities.confirm-password>
                @else
                    <x-utilities.confirm-password wire:then="showRecoveryCodes" class="btn-neutral" wire:loading.attr="disabled">
                        {{ __('Show Codes') }} <i class="bi bi-eye-fill ms-2"></i>
                    </x-utilities.confirm-password>
                @endif

                @if ($showingConfirmation)
                    <x-utilities.confirm-password wire:then="disableTwoFactorAuthentication" class="btn-neutral" wire:loading.attr="disabled">
                        {{ __('Cancel') }} <i class="bi bi-x-lg ms-2"></i>
                    </x-utilities.confirm-password>
                @else
                    <x-utilities.confirm-password wire:then="disableTwoFactorAuthentication" class="btn-outline-danger" wire:loading.attr="disabled">
                        {{ __('Disable') }} <i class="bi bi-x-lg ms-2"></i>
                    </x-utilities.confirm-password>
                @endif
            @endif
        </div>
    </div>
    <hr class="my-6">
    <div class="my-6">
        @if ($this->enabled)
            @if ($showingConfirmation)
                <x-alerts.alert type="info">
                    <i class="bi bi-info-circle-fill me-2"></i> {{ __('Finish enabling two factor authentication.') }}
                </x-alerts.alert>
            @else
                <x-alerts.alert type="success">
                    <i class="bi bi-check-circle-fill me-2"></i> {{ __('You have enabled two factor authentication.') }}
                </x-alerts.alert>
            @endif
        @else
            <x-alerts.alert type="warning">
                <i class="bi bi-exclamation-circle-fill me-2"></i> {{ __('You have not enabled two factor authentication.') }}
            </x-alerts.alert>
        @endif
    </div>
    <div>
        @if ($this->enabled)
            @if ($showingQrCode)
                <div class="d-block d-md-flex">
                    <div class="d-inline-flex flex-md-fill">
                        <div class="p-2 bg-white">
                            {!! $this->user->twoFactorQrCodeSvg() !!}
                        </div>
                    </div>
                    <div class="flex-fill ms-0 ms-md-6 mt-4 mt-md-0">
                        <p class="text-muted mb-3">
                            @if ($showingConfirmation)
                                {{ __('To finish enabling two factor authentication, scan the following QR code using your phone\'s authenticator application or enter the setup key and provide the generated OTP code.') }}
                            @else
                                {{ __('Two factor authentication is now enabled. Scan the following QR code using your phone\'s authenticator application or enter the setup key.') }}
                            @endif
                        </p>
                        <p class="mb-4">
                            {{ __('Setup Key') }}: <strong>{{ decrypt($this->user->two_factor_secret) }}</strong>
                        </p>
                        @if ($showingConfirmation)
                            <div class="mt-4">
                                @php($uuid = Str::random(8))
                                <x-forms.label :label="__('Code')" for="{{ $uuid }}" />
                                <x-forms.inputs.input name="code" id="{{ $uuid }}" inputmode="numeric" autofocus autocomplete="one-time-code"
                                                      wire:model="code"
                                                      wire:keydown.enter="confirmTwoFactorAuthentication" />
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            @if ($showingRecoveryCodes)
                <div class="my-4">
                    <p class="text-muted">
                        {{ __('Store these recovery codes in a secure password manager. They can be used to recover access to your account if your two factor authentication device is lost.') }}
                    </p>
                </div>
                <x-cards.card>
                    <div class="d-grid gap-3">
                        @foreach (json_decode(decrypt($this->user->two_factor_recovery_codes), true) as $code)
                            <div>{{ $code }}</div>
                        @endforeach
                    </div>
                </x-cards.card>
            @endif
        @endif
    </div>
    <hr class="my-6 d-md-none">
    <div class="d-md-none">
        @if (! $this->enabled)
            <x-utilities.confirm-password wire:then="enableTwoFactorAuthentication" class="btn-outline-primary w-100" wire:loading.attr="disabled">
                {{ __('Enable') }} <i class="bi bi-lock-fill ms-2"></i>
            </x-utilities.confirm-password>
        @else
            @if ($showingRecoveryCodes)
                <x-utilities.confirm-password wire:then="regenerateRecoveryCodes" class="btn-neutral w-100 mb-4" wire:loading.attr="disabled">
                    {{ __('Regenerate Codes') }} <i class="bi bi-arrow-clockwise ms-2"></i>
                </x-utilities.confirm-password>
            @elseif ($showingConfirmation)
                <x-utilities.confirm-password wire:then="confirmTwoFactorAuthentication" class="btn-outline-primary w-100 mb-4" wire:loading.attr="disabled">
                    {{ __('Confirm') }} <i class="bi bi-check-lg ms-2"></i>
                </x-utilities.confirm-password>
            @else
                <x-utilities.confirm-password wire:then="showRecoveryCodes" class="btn-neutral w-100 mb-4" wire:loading.attr="disabled">
                    {{ __('Show Codes') }} <i class="bi bi-eye-fill ms-2"></i>
                </x-utilities.confirm-password>
            @endif

            @if ($showingConfirmation)
                <x-utilities.confirm-password wire:then="disableTwoFactorAuthentication" class="btn-neutral w-100" wire:loading.attr="disabled">
                    {{ __('Cancel') }} <i class="bi bi-x-lg ms-2"></i>
                </x-utilities.confirm-password>
            @else
                <x-utilities.confirm-password wire:then="disableTwoFactorAuthentication" class="btn-outline-danger w-100" wire:loading.attr="disabled">
                    {{ __('Disable') }} <i class="bi bi-x-lg ms-2"></i>
                </x-utilities.confirm-password>
            @endif
        @endif
    </div>
</div>
