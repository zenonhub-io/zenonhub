<div>
    <form wire:submit="verifySignature">
        <div class="vstack gap-6">
            @if ($result !== null)
                <div class="row align-items-center">
                    <div class="col-lg-16">
                        @if ($result === true)
                            <x-alerts.alert type="success">
                                <i class="bi bi-check-circle-fill me-2"></i> {{ __('Signature verified') }}
                            </x-alerts.alert>
                        @elseif ($result === false)
                            <x-alerts.alert type="danger">
                                <i class="bi bi-exclamation-circle-fill me-2"></i> {{ __('Signature verified') }}
                            </x-alerts.alert>
                        @endif
                    </div>
                </div>
            @endif
            <div class="row align-items-center">
                @php($uuid = Str::random(8))
                <div class="col-lg-4">
                    <x-forms.label :label="__('Address')" for="{{ $uuid }}" />
                </div>
                <div class="col-lg-12">
                    <x-forms.inputs.input name="verifySignatureForm.address" id="{{ $uuid }}" wire:model="verifySignatureForm.address" />
                </div>
            </div>
            <div class="row align-items-center">
                @php($uuid = Str::random(8))
                <div class="col-lg-4">
                    <x-forms.label :label="__('Public Key')" for="{{ $uuid }}" />
                </div>
                <div class="col-lg-12">
                    <x-forms.inputs.input name="verifySignatureForm.publicKey" id="{{ $uuid }}" wire:model="verifySignatureForm.publicKey" />
                </div>
            </div>
            <div class="row align-items-center">
                @php($uuid = Str::random(8))
                <div class="col-lg-4">
                    <x-forms.label :label="__('Message')" for="{{ $uuid }}" />
                </div>
                <div class="col-lg-12">
                    <x-forms.inputs.input name="verifySignatureForm.message" id="{{ $uuid }}" wire:model="verifySignatureForm.message" />
                </div>
            </div>
            <div class="row align-items-center">
                @php($uuid = Str::random(8))
                <div class="col-lg-4">
                    <x-forms.label :label="__('Signature')" for="{{ $uuid }}" />
                </div>
                <div class="col-lg-12">
                    <x-forms.inputs.input name="verifySignatureForm.signature" id="{{ $uuid }}" wire:model="verifySignatureForm.signature" />
                </div>
            </div>
            <div class="row align-items-center">
                <div class="col-lg-16">
                    <button type="submit" class="btn w-100 btn-outline-primary">
                        <i class="bi bi-check-lg me-2"></i>
                        Verify
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>
