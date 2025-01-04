<x-auth-layout>

    <header class="header mb-6 border-bottom-0 border-bottom-md">
        <x-includes.header-title :title="__('Sign in!')" class="mb-4" />
        <ul class="nav nav-tabs nav-fill nav-tabs-flush gap-6 overflow-x border-0 lead">
            <li class="nav-item">
                <x-link :href="route('login')" class="nav-link active">{{ __('Login') }}</x-link>
            </li>
            <li class="nav-item">
                <x-link :href="route('register')" class="nav-link">{{ __('Register') }}</x-link>
            </li>
        </ul>
    </header>

    @if (session('status'))
        <x-alerts.alert type="success" class="mb-4">
            <i class="bi bi-info-circle-fill me-2"></i> {{ session('status') }}
        </x-alerts.alert>
    @endif

    <x-forms.form :action="route('login')" class="needs-validation">
        <x-honeypot />
        <x-forms.inputs.hidden name="redirect" value="{{ request()->input('redirect') }}" />
        <div class="mb-4">
            @php($uuid = Str::random(8))
            <x-forms.label :label="__('Email')" for="{{ $uuid }}" />
            <x-forms.inputs.email name="email" id="{{ $uuid }}" class="form-control-lg" autocomplete="username" autofocus required />
        </div>
        <div class="mb-4">
            @php($uuid = Str::random(8))
            <div class="d-flex justify-content-between align-items-center">
                <x-forms.label :label="__('Password')" for="{{ $uuid }}" />
                <x-link class="fs-6 text-muted text-light-hover" :href="route('password.request')" tabindex="-1">
                    {{ __('Forgot your password?') }}
                </x-link>
            </div>
            <x-forms.inputs.password name="password" id="{{ $uuid }}" class="form-control-lg" required />
        </div>
        <x-forms.inputs.checkbox :label="__('Remember me')" name="remember" />
        <hr class="my-6">
        <x-buttons.button type="submit" class="btn-outline-primary w-100">
            {{ __('Sign In') }} <i class="bi bi-arrow-right ms-2"></i>
        </x-buttons.button>
    </x-forms.form>
</x-auth-layout>
