<x-auth-layout>
    <x-includes.header :title="__('Reset your password')" centered />

    @if (session('status'))
        <x-alerts.alert type="success" class="mb-4">
            <i class="bi bi-info-circle-fill me-2"></i> {{ session('status') }}
        </x-alerts.alert>
    @endif

    <x-forms.form :action="route('password.update')" class="needs-validation">
        <x-forms.inputs.hidden name="token" :value="$request->route('token')" />
        <div class="mb-4">
            @php($uuid = Str::random(8))
            <x-forms.label :label="__('Email')" for="{{ $uuid }}" />
            <x-forms.inputs.email name="email" id="{{ $uuid }}" class="form-control-lg" :value="old('email', $request->email)" autocomplete="username" autofocus required />
        </div>
        <div class="mb-4">
            @php($uuid = Str::random(8))
            <x-forms.label :label="__('Password')" for="{{ $uuid }}" />
            <x-forms.inputs.password name="password" id="{{ $uuid }}" class="form-control-lg" required autocomplete="new-password" />
        </div>
        <div>
            @php($uuid = Str::random(8))
            <x-forms.label :label="__('Confirm Password')" for="{{ $uuid }}" />
            <x-forms.inputs.password name="password_confirmation" id="{{ $uuid }}" class="form-control-lg" required />
        </div>
        <hr class="my-6">
        <x-buttons.button type="submit" class="btn-outline-primary w-100">
            {{ __('Reset Password') }} <i class="bi bi-arrow-right ms-2"></i>
        </x-buttons.button>
    </x-forms.form>
</x-auth-layout>
