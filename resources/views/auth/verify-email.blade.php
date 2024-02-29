<x-auth-layout>
    <x-includes.header :title="__('Verify your email')" centered />

    <div class="mb-4">
        {{ __('Before continuing, please verify your email address by clicking on the link we emailed to you. If you didn\'t receive the email, you can request another to be sent.') }}
    </div>

    @if (session('status') === 'verification-link-sent')
        <x-alerts.alert type="success" class="mb-4">
            <i class="bi bi-info-circle-fill me-2"></i> {{ __('A new verification link has been sent to the email address you provided in your profile settings.') }}
        </x-alerts.alert>
    @endif

    <x-forms.form :action="route('verification.send')">
        <x-buttons.button type="submit" class="btn-outline-primary w-100">
            {{ __('Resend Verification Email') }} <i class="bi bi-arrow-right ms-2"></i>
        </x-buttons.button>
    </x-forms.form>
    <hr class="my-6">
    <x-buttons.logout :action="route('logout')" class="btn btn-dark w-100">
        <i class="bi bi-lock-fill me-1"></i> {{ __('Logout') }}
    </x-buttons.logout>
</x-auth-layout>
