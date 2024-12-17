@props([
	'title' => __('Confirm Password'),
	'content' => __('For your security, please confirm your password to continue.'),
    'button' => __('Confirm'),
])

@php
    $confirmableId = md5($attributes->wire('then'));
@endphp

<button type="button" {{ $attributes->wire('then') }} {{ $attributes->merge(['class' => 'btn']) }}
        x-data
        x-ref="button"
        x-on:click="$wire.startConfirmingPassword('{{ $confirmableId }}');"
        x-on:password-confirmed.window="
            setTimeout(
                () => $event.detail.id === '{{ $confirmableId }}' && $refs.button.dispatchEvent(new CustomEvent('then', { bubbles: false })
            ), 250);
        "
        x-on:stop-confirming-password.window="window.bootstrap.Modal.getOrCreateInstance(document.getElementById('password-confirmation-modal')).hide();"
        x-on:confirming-password.window="window.bootstrap.Modal.getOrCreateInstance(document.getElementById('password-confirmation-modal')).show();"
>
    {{ $slot }}
</button>

@once
    <div id="password-confirmation-modal" class="modal fade" tabindex="-1"
         wire:ignore.self
         data-bs-backdrop="static"
    >
        <div class="modal-dialog">
            <div class="modal-content">

                <x-modals.heading :title="$title" />

                <div class="modal-body">
                    {{ $content }}

                    <div class="mt-4" x-data="{}">
                        @php($uuid = Str::random(8))
                        <x-forms.label :label="__('Your password')" for="{{ $uuid }}" />
                        <x-forms.inputs.password name="confirmable_password" id="{{ $uuid }}" autocomplete="current-password"
                                                 x-ref="confirmable_password"
                                                 wire:model="confirmablePassword"
                                                 wire:keydown.enter="confirmPassword" />
                    </div>
                </div>

                <x-modals.footer>
                    <x-buttons.button type="button" class="btn-neutral" wire:click="stopConfirmingPassword" wire:loading.attr="disabled">
                        {{ __('Cancel') }}
                    </x-buttons.button>

                    <x-buttons.button class="btn-outline-primary ms-3" wire:click="confirmPassword" wire:loading.attr="disabled">
                        {{ $button }}
                    </x-buttons.button>
                </x-modals.footer>
            </div>
        </div>
    </div>
@endonce
