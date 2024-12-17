<x-auth-layout>
    <x-includes.header :title="__('Sign up!')" centered>
        <ul class="nav nav-tabs nav-fill nav-tabs-flush gap-6 overflow-x border-0">
            <li class="nav-item">
                <x-link :href="route('login')" class="nav-link">{{ __('Login') }}</x-link>
            </li>
            <li class="nav-item">
                <x-link :href="route('register')" class="nav-link active">{{ __('Register') }}</x-link>
            </li>
        </ul>
    </x-includes.header>
    <x-forms.form :action="route('register')" class="needs-validation">
        <x-honeypot />
        <div class="mb-4">
            @php($uuid = Str::random(8))
            <x-forms.label :label="__('Username')" for="{{ $uuid }}" />
            <x-forms.inputs.input name="username" id="{{ $uuid }}" class="form-control-lg" autocomplete="username" required />
        </div>
        <div class="mb-4">
            @php($uuid = Str::random(8))
            <x-forms.label :label="__('Email')" for="{{ $uuid }}" />
            <x-forms.inputs.email name="email" id="{{ $uuid }}" class="form-control-lg" required />
        </div>
        <div class="mb-4">
            @php($uuid = Str::random(8))
            <x-forms.label :label="__('Password')" for="{{ $uuid }}" />
            <x-forms.inputs.password name="password" id="{{ $uuid }}" class="form-control-lg" required />
        </div>
        <div class="mb-4">
            @php($uuid = Str::random(8))
            <x-forms.label :label="__('Confirm Password')" for="{{ $uuid }}" />
            <x-forms.inputs.password name="password_confirmation" id="{{ $uuid }}" class="form-control-lg" required />
        </div>
        <x-forms.inputs.checkbox name="terms">
            {!! __('I agree to the :privacy_policy', [
                'privacy_policy' => '<a href="'.route('policy').'" target="_blank">'.__('Privacy Policy').'</a>',
            ]) !!}
        </x-forms.inputs.checkbox>
        <hr class="my-6">
        <x-buttons.button type="submit" class="btn-outline-primary w-100">
            {{ __('Sign Up') }} <i class="bi bi-arrow-right ms-2"></i>
        </x-buttons.button>
    </x-forms.form>
</x-auth-layout>
