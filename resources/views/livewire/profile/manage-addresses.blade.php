<div>
    <div class="d-flex align-items-end justify-content-between align-items-center">
        <div>
            <h4 class="mb-1">{{ __('Verified Addresses') }}</h4>
            <p class="text-muted">{{ __('Verify your addresses for enhanced functionality.') }}</p>
        </div>
        <div class="d-none d-md-flex gap-2">
            <x-buttons.button class="btn btn-outline-primary" wire:click="verifyAddress" wire:loading.attr="disabled">
                {{ __('Add') }} <i class="bi bi-check-lg ms-2"></i>
            </x-buttons.button>
        </div>
    </div>
    <div class="d-none d-md-block">
        <x-alerts.response class="alert-success my-6" on="profile.address.verified">
            <i class="bi bi-check-circle-fill me-2"></i> {{ __('Your address has been verified') }}
        </x-alerts.response>
    </div>
    <hr class="my-6">
    <div class="vstack gap-6">
        <div class="row align-items-center">
            @php($uuid = Str::random(8))
            <div class="col-md-4">
                <x-forms.label :label="__('Address')" for="{{ $uuid }}" />
            </div>
            <div class="col-md-12">
                <x-forms.inputs.input name="address" id="{{ $uuid }}" wire:model="verifyAddressForm.address" />
            </div>
        </div>
        <div class="row align-items-center">
            @php($uuid = Str::random(8))
            <div class="col-md-4">
                <x-forms.label :label="__('Nickname')" for="{{ $uuid }}" />
            </div>
            <div class="col-md-12">
                <x-forms.inputs.input name="nickname" id="{{ $uuid }}" wire:model="verifyAddressForm.nickname" />
            </div>
        </div>
        <div class="row align-items-center">
            @php($uuid = Str::random(8))
            <div class="col-md-4">
                <x-forms.label :label="__('Message')" for="{{ $uuid }}" />
            </div>
            <div class="col-md-12">
                <x-forms.inputs.input name="message" id="{{ $uuid }}" wire:model="verifyAddressForm.message" :readonly="true" />
            </div>
        </div>
        <div class="row align-items-center">
            @php($uuid = Str::random(8))
            <div class="col-md-4">
                <x-forms.label :label="__('Signature')" for="{{ $uuid }}" />
            </div>
            <div class="col-md-12">
                <x-forms.inputs.input name="signature" id="{{ $uuid }}" wire:model="verifyAddressForm.signature" />
            </div>
        </div>
        {{--        <div class="row align-items-center">--}}
        {{--            @php($uuid = Str::random(8))--}}
        {{--            <div class="col-md-4">--}}
        {{--                <x-forms.label :label="__('Public Key')" for="{{ $uuid }}" />--}}
        {{--            </div>--}}
        {{--            <div class="col-md-12">--}}
        {{--                <x-forms.inputs.input name="public_key" id="{{ $uuid }}" wire:model="verifyAddressForm.public_key" />--}}
        {{--            </div>--}}
        {{--        </div>--}}
    </div>
    <hr class="my-6 d-md-none">
    <div class="d-md-none">
        <x-buttons.button class="btn btn-outline-primary w-100" wire:click="verifyAddress" wire:loading.attr="disabled">
            {{ __('Add') }} <i class="bi bi-check-lg ms-2"></i>
        </x-buttons.button>
    </div>

    @if ($this->user->verifiedAccounts->isNotEmpty())
        <hr class="my-6">
        <div class="list-group shadow">
            @foreach ($this->user->verifiedAccounts->sortBy('verified_at') as $account)
                <div class="list-group-item d-flex align-items-center">
                    <div class="flex-fill">
                        <span class="d-block text-sm text-heading fw-semibold">{{ $account->pivot->nickname }}</span>
                        <span class="d-block text-sm text-heading fw-semibold"><x-address :account="$account" :named="false" /></span>
                        <div class="d-block text-xs text-muted mt-2">
                            {{ __('Verified at') }}: <x-date-time.carbon :date="$account->pivot->verified_at" class="d-inline fw-bold" />
                        </div>
                    </div>
                    <div class="ms-auto d-flex gap-4 align-items-center">
                        <x-buttons.button wire:click="confirmAddressDeletion('{{ $account->address }}')" class="btn-outline-danger btn-sm">
                            {{ __('Delete') }} <i class="bi bi-trash-fill ms-2"></i>
                        </x-buttons.button>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    <x-modals.modal id="confirm-delete-address">
        <x-slot:heading>
            {{ __('Delete verified address') }}
        </x-slot:heading>
        {{ __('Are you sure you would like to delete this address?') }}
        <x-slot:footer>
            <x-buttons.button class="btn btn-neutral" data-bs-dismiss="modal">
                {{ __('Cancel') }}
            </x-buttons.button>
            <x-buttons.button class="btn btn-outline-danger" wire:click="deleteAddress">
                {{ __('Confirm') }} <i class="bi bi-check-lg ms-2"></i>
            </x-buttons.button>
        </x-slot:footer>
    </x-modals.modal>
</div>
