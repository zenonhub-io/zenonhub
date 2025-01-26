<x-auth-layout>

    <x-includes.auth-header :title="__('Confirm your password')" :responsive-border="true" />

    <div class="mb-4">
        {{ __('This is a secure area of the application. Please confirm your password before continuing.') }}
    </div>

    @if (session('status'))
        <x-alerts.alert type="success" class="mb-4">
            <i class="bi bi-info-circle-fill me-2"></i> {{ session('status') }}
        </x-alerts.alert>
    @endif

    <x-forms.form :action="route('password.confirm')" class="needs-validation">
        <x-honeypot />
        <div>
            @php($uuid = Str::random(8))
            <x-forms.label :label="__('Password')" for="{{ $uuid }}" />
            <x-forms.inputs.password name="password" id="{{ $uuid }}" class="form-control-lg" />
        </div>
        <hr class="my-6">
        <x-buttons.button type="submit" class="btn-outline-primary w-100">
            {{ __('Confirm') }} <i class="bi bi-arrow-right ms-2"></i>
        </x-buttons.button>
    </x-forms.form>
</x-auth-layout>
