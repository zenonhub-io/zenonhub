<div>
    <div class="card shadow mb-4">
        <div class="card-header border-bottom">
            <h4 class="mb-0">Verify signature</h4>
        </div>
        <div class="card-body">
            <p>
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
                <div class="row mb-4 align-items-center">
                    <label for="form-address" class="form-label col-md-6">Address</label>
                    <div class="col-md-18">
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
                <div class="row mb-4 align-items-center">
                    <label for="form-public-key" class="form-label col-md-6">Public key</label>
                    <div class="col-md-18">
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
                <div class="row mb-4 align-items-center">
                    <label for="form-message" class="form-label col-md-6">Message</label>
                    <div class="col-md-18">
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
                <div class="row mb-4 align-items-center">
                    <label for="form-signature" class="form-label col-md-6">Signature</label>
                    <div class="col-md-18">
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
                <button type="submit" class="btn w-100 btn-outline-primary">
                    <i class="bi bi-check-lg me-2"></i>
                    Verify
                </button>
            </form>
        </div>
    </div>
</div>
