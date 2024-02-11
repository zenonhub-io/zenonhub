<div>
    <div class="card shadow mb-4">
        <div class="card-header">
            <h4 class="mb-0">Broadcast message</h4>
        </div>
        <div class="card-body">
            <p>
                Pillar owners can use this tool to broadcast a message to the <a href="https://forum.zenon.org/c/zenon/pillar-messages/20">Pillar Messages</a> forum category. Sign the message in SYRIUS and submit the generated signature along with your post.
            </p>
            <hr class="border-secondary my-4">
            @if ($result === true)
                <x-alert
                    message="Message sent"
                    type="success"
                    icon="check-circle-fill"
                    class="mb-3"
                />
            @elseif ($result === false)
                <x-alert
                    message="{{ $this->error ?: 'Error sending message, double check the signature' }}"
                    type="danger"
                    icon="exclamation-octagon"
                    class="mb-3"
                />
            @endif
            <form wire:submit.prevent="submit">
                <div class="row mb-4 align-items-center">
                    <label for="form-address" class="form-label col-md-6">Pillar</label>
                    <div class="col-md-18">
                        <select
                            id="form-address"
                            class="form-select @error('address')is-invalid @enderror"
                            wire:model="address"
                        >
                            <option value="null">Choose pillar</option>
                            @foreach ($pillars as $pillar)
                                <option value="{{ $pillar->owner->address }}">{{ $pillar->name }}</option>
                            @endforeach
                        </select>
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
                        <div class="input-group">
                            <input
                                type="text"
                                id="form-message"
                                class="form-control"
                                readonly
                                wire:model.defer="message"
                            >
                            <span class="input-group-text js-copy" data-clipboard-target="#form-message" data-bs-toggle="tooltip" data-bs-title="Copy">
                                <i class="bi-clipboard text-zenon-blue"></i>
                            </span>
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
                <div class="row mb-4 align-items-center">
                    <label for="form-title" class="form-label col-md-6">Title</label>
                    <div class="col-md-18">
                        <input
                            type="text"
                            id="form-title"
                            name="signature"
                            class="form-control @error('title')is-invalid @enderror"
                            wire:model="title"
                        >
                        <div class="invalid-feedback">
                            @error('title') {{ $message }} @enderror
                        </div>
                    </div>
                </div>
                <div class="row mb-4 align-items-center">
                    <label for="form-post" class="form-label col-md-6">Post</label>
                    <div class="col-md-18">
                        <textarea
                            id="form-post"
                            name="post"
                            class="form-control @error('post')is-invalid @enderror"
                            wire:model="post"
                        ></textarea>
                        <div class="invalid-feedback">
                            @error('post') {{ $message }} @enderror
                        </div>
                    </div>
                </div>
                <button type="submit" class="btn w-100 btn-outline-primary">
                    <i class="bi bi-broadcast me-2"></i>
                    Broadcast message
                </button>
            </form>
        </div>
    </div>
</div>
