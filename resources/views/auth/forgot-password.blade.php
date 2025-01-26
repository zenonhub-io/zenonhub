<x-auth-layout>

    <x-includes.auth-header :title="__('Forgot your password?')" :responsive-border="true" />

    <div class="mb-4">
        {{ __('Just let us know your email address and we will send you a password reset link that will allow you to choose a new one.') }}
    </div>

    @if (session('status'))
        <x-alerts.alert type="success" class="mb-4">
            <i class="bi bi-info-circle-fill me-2"></i> {{ session('status') }}
        </x-alerts.alert>
    @endif

    <x-forms.form :action="route('password.email')" class="needs-validation">
        <x-honeypot />
        <div>
            @php($uuid = Str::random(8))
            <x-forms.label :label="__('Email')" for="{{ $uuid }}" />
            <x-forms.inputs.email name="email" id="{{ $uuid }}" class="form-control-lg" />
        </div>
        <hr class="my-6">
        <x-buttons.button type="submit" class="btn-outline-primary w-100 mb-4">
            {{ __('Email Password Reset Link') }} <i class="bi bi-arrow-right ms-2"></i>
        </x-buttons.button>
        <x-link :href="route('login')" class="btn btn-neutral w-100">
            <i class="bi bi-arrow-left me-2"></i> {{ __('Back to Login') }}
        </x-link>
    </x-forms.form>
</x-auth-layout>
