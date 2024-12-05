<div class="m-n6">

    <x-modals.heading :title="__('Manage Profile: ').$title" />

    <div class="p-6">
        <div class="vstack gap-4">

            @if (! $hasUserVerifiedAddress)
                <p>To update your profile, signature verification for the following address is required to prove ownership:</p>
                <div class="p-4 my-2 border rounded bg-body-tertiary shadow-inset">
                    {{ $address }}
                </div>
                <p>Alternatively <x-link :href="route('login')">login</x-link> to your account and <x-link :href="route('profile', ['tab' => 'addresses'])">link</x-link> the address to your profile for faster updates in the future.</p>
                <hr>
            @endif

            <x-forms.inputs.hidden name="address" value="{{ $address }}" wire:model="address" />
{{--            <div class="row align-items-center">--}}
{{--                @php($uuid = Str::random(8))--}}
{{--                <div class="col-md-4">--}}
{{--                    <x-forms.label :label="__('Bio')" for="{{ $uuid }}" />--}}
{{--                </div>--}}
{{--                <div class="col-md-20">--}}
{{--                    <x-forms.inputs.textarea name="bio" id="{{ $uuid }}" wire:model="socialProfileForm.bio" />--}}
{{--                </div>--}}
{{--            </div>--}}
            <div class="row align-items-center">
                @php($uuid = Str::random(8))
                <div class="col-md-4">
                    <x-forms.label :label="__('Avatar')" for="{{ $uuid }}" />
                </div>
                <div class="col-md-20">
                    <x-forms.inputs.input name="avatar" id="{{ $uuid }}" placeholder="https://example.com/avatar.png" wire:model="socialProfileForm.avatar "/>
                </div>
            </div>

            <hr>

            <div class="row align-items-center">
                @php($uuid = Str::random(8))
                <div class="col-md-4">
                    <x-forms.label :label="__('Website')" for="{{ $uuid }}" />
                </div>
                <div class="col-md-20">
                    <x-forms.inputs.input name="website" id="{{ $uuid }}" placeholder="https://example.com" wire:model="socialProfileForm.website "/>
                </div>
            </div>
            <div class="row align-items-center">
                @php($uuid = Str::random(8))
                <div class="col-md-4">
                    <x-forms.label :label="__('Email')" for="{{ $uuid }}" />
                </div>
                <div class="col-md-20">
                    <x-forms.inputs.email name="email" id="{{ $uuid }}" placeholder="me@example.com"  wire:model="socialProfileForm.email" />
                </div>
            </div>
            <div class="row align-items-center">
                @php($uuid = Str::random(8))
                <div class="col-md-4">
                    <x-forms.label :label="__('X')" for="{{ $uuid }}" />
                </div>
                <div class="col-md-20">
                    <x-forms.inputs.input name="x" id="{{ $uuid }}" placeholder="https://x.com/username" wire:model="socialProfileForm.x "/>
                </div>
            </div>
            <div class="row align-items-center">
                @php($uuid = Str::random(8))
                <div class="col-md-4">
                    <x-forms.label :label="__('Telegram')" for="{{ $uuid }}" />
                </div>
                <div class="col-md-20">
                    <x-forms.inputs.input name="telegram" id="{{ $uuid }}" placeholder="https://t.me/username" wire:model="socialProfileForm.telegram "/>
                </div>
            </div>
            <div class="row align-items-center">
                @php($uuid = Str::random(8))
                <div class="col-md-4">
                    <x-forms.label :label="__('GitHub')" for="{{ $uuid }}" />
                </div>
                <div class="col-md-20">
                    <x-forms.inputs.input name="github" id="{{ $uuid }}" placeholder="https://github.com/username" wire:model="socialProfileForm.github "/>
                </div>
            </div>

            <div class="row align-items-center">
                @php($uuid = Str::random(8))
                <div class="col-md-4">
                    <x-forms.label :label="__('Medium')" for="{{ $uuid }}" />
                </div>
                <div class="col-md-20">
                    <x-forms.inputs.input name="medium" id="{{ $uuid }}" placeholder="https://medium.com/username" wire:model="socialProfileForm.medium "/>
                </div>
            </div>

            @if (! $hasUserVerifiedAddress)
                <hr>

                <div class="row align-items-center">
                    @php($uuid = Str::random(8))
                    <div class="col-md-4">
                        <x-forms.label :label="__('Message')" for="{{ $uuid }}" />
                    </div>
                    <div class="col-md-20">
                        <x-forms.inputs.input name="message" id="{{ $uuid }}" wire:model="message" :readonly="true" />
                    </div>
                </div>
                <div class="row align-items-center">
                    @php($uuid = Str::random(8))
                    <div class="col-md-4">
                        <x-forms.label :label="__('Signature')" for="{{ $uuid }}" />
                    </div>
                    <div class="col-md-20">
                        <x-forms.inputs.input name="signature" id="{{ $uuid }}" wire:model="signature" />
                        <p class="text-muted text-sm">To sign the message you can use Syrius: Settings > Security > Sign</p>
                    </div>
                </div>
            @endif

        </div>
    </div>

    <div class="d-none d-md-block">
        <x-alerts.response class="alert-success mx-6 mb-6" on="social-profile.updated">
            <i class="bi bi-check-circle-fill me-2"></i> {{ __('Your profile has been updated') }}
        </x-alerts.response>
    </div>

    <div class="modal-footer">
        <x-buttons.button class="btn btn-neutral" data-bs-dismiss="modal">
            {{ __('Cancel') }}
        </x-buttons.button>
        <x-buttons.button class="btn btn-outline-success" wire:click="saveProfile">
            {{ __('Save') }} <i class="bi bi-check-lg ms-2"></i>
        </x-buttons.button>
    </div>
</div>
