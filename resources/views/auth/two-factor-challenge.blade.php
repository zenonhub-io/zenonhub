<x-auth-layout>

    <x-includes.auth-header :title="__('Confirm Access')" :responsive-border="true" />

    <div x-data="{ recovery: false }">
        <div class="mb-4" x-show="! recovery">
            {{ __('Please confirm access to your account by entering the authentication code provided by your authenticator application.') }}
        </div>
        <div class="mb-4" x-cloak x-show="recovery">
            {{ __('Please confirm access to your account by entering one of your emergency recovery codes.') }}
        </div>
        <x-forms.form :action="route('two-factor.login')" class="needs-validation">
            <x-honeypot />
            <div class="mb-4" x-show="! recovery">
                @php($uuid = Str::random(8))
                <x-forms.label :label="__('Code')" for="{{ $uuid }}" />
                <x-forms.inputs.input name="code" id="{{ $uuid }}" class="form-control-lg" inputmode="numeric" autofocus x-ref="code" autocomplete="one-time-code" />
            </div>
            <div class="mb-4" x-cloak x-show="recovery">
                @php($uuid = Str::random(8))
                <x-forms.label :label="__('Recovery code')" for="{{ $uuid }}" />
                <x-forms.inputs.input name="recovery_code" id="{{ $uuid }}" class="form-control-lg" x-ref="recovery_code" autocomplete="one-time-code" />
            </div>
            <hr class="my-6">
            <x-buttons.button type="submit" class="btn-outline-primary w-100">
                {{ __('Login') }} <i class="bi bi-arrow-right ms-2"></i>
            </x-buttons.button>
            <hr>
            <x-buttons.button class="btn-neutral w-100"
                              x-show="! recovery"
                              x-on:click="
                    recovery = true;
                    $nextTick(() => { $refs.recovery_code.focus() })
            ">
                {{ __('Use a recovery code') }}
            </x-buttons.button>
            <x-buttons.button class="btn-neutral w-100"
                              x-cloak
                              x-show="recovery"
                              x-on:click="
                    recovery = false;
                    $nextTick(() => { $refs.code.focus() })
            ">
                {{ __('Use an authentication code') }}
            </x-buttons.button>
        </x-forms.form>
    </div>
</x-auth-layout>
