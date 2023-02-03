<div>
    <div class="card shadow mb-4">
        <div class="card-header border-bottom">
            <h4 class="mb-0">Verify signature</h4>
        </div>
        <div class="card-body">
            <p class="text-white-70">
                Don't trust, verify. Fill in the form below to verify if a message signature is valid and matches the supplied address. Alternatively use our <a href="{{ route('tools.api-playground', ['request' => 'Utilities.verifySignedMessage']) }}">API</a> to verify messages in your own app.
            </p>
            <hr class="border-secondary my-4">
            @if ($result === true)
                <x-alert
                    message="Signature verified"
                    type="success"
                    icon="check-circle-fill"
                    class="mb-3"
                />
            @elseif ($result === false)
                <x-alert
                    message="Invalid signature"
                    type="danger"
                    icon="exclamation-octagon"
                    class="mb-4"
                />
            @endif
            <form wire:submit.prevent="submit">
                <div class="row mb-4">
                    <label for="form-address" class="col-sm-6 col-form-label form-label">Address</label>
                    <div class="col-sm-18">
                        <input
                            type="text"
                            id="form-address"
                            name="address"
                            class="form-control @error('address')is-invalid @enderror"
                            wire:model="address"
                        >
                        <div class="invalid-feedback">
                            @error('address') {{ $message }} @enderror
                        </div>
                    </div>
                </div>
                <div class="row mb-4">
                    <label for="form-public-key" class="col-sm-6 col-form-label form-label">Public key</label>
                    <div class="col-sm-18">
                        <input
                            type="text"
                            id="form-public-key"
                            name="public_key"
                            class="form-control @error('public_key')is-invalid @enderror"
                            wire:model="public_key"
                        >
                        <div class="invalid-feedback">
                            @error('public_key') {{ $message }} @enderror
                        </div>
                    </div>
                </div>
                <div class="row mb-4">
                    <label for="form-message" class="col-sm-6 col-form-label form-label">Message</label>
                    <div class="col-sm-18">
                        <input
                            type="text"
                            id="form-message"
                            name="message"
                            class="form-control @error('message')is-invalid @enderror"
                            wire:model="message"
                        >
                        <div class="invalid-feedback">
                            @error('message') {{ $message }} @enderror
                        </div>
                    </div>
                </div>
                <div class="row mb-4">
                    <label for="form-signature" class="col-sm-6 col-form-label form-label">Signature</label>
                    <div class="col-sm-18">
                        <input
                            type="text"
                            id="form-signature"
                            name="signature"
                            class="form-control @error('signature')is-invalid @enderror"
                            wire:model="signature"
                        >
                        <div class="invalid-feedback">
                            @error('signature') {{ $message }} @enderror
                        </div>
                    </div>
                </div>
                <div class="row mb-0">
                    <div class="col-24">
                        <button type="submit" class="btn w-100 btn-primary">
                            {!! svg('verify-circle', 'me-2') !!}
                            Verify
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
