<div>
    <div class="card shadow mb-4">
        <div class="card-header">
            <h4 class="mb-0">Broadcast message</h4>
        </div>
        <div class="card-header">
            <div class="d-md-none">
                <select id="broadcasting-sections" class="form-control" wire:change="$set('tab', $event.target.value)">
                    <option value="send" {{ $tab === 'send' ? 'selected' : '' }}>Send</option>
                    <option value="history" {{ $tab === 'history' ? 'selected' : '' }}>History</option>
                </select>
            </div>
            <div class="d-none d-md-block">
                <ul class="nav nav-tabs-alt card-header-tabs">
                    <li class="nav-item">
                        <button class="btn nav-link {{ $tab === 'send' ? 'active' : '' }}" wire:click="$set('tab', 'send')">
                            <i class="bi bi-send opacity-70 me-2"></i> Send
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="btn nav-link {{ $tab === 'history' ? 'active' : '' }}" wire:click="$set('tab', 'history')">
                            <i class="bi bi-clock-history opacity-70 me-2"></i> History
                        </button>
                    </li>
                </ul>
            </div>
        </div>
        <div class="card-body">
            <div class="tab-content">
                <div class="tab-pane fade show active">
                    @if ($tab === 'send')
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
                            <div class="row mb-4">
                                <label for="form-address" class="col-sm-6 col-form-label form-label">Pillar</label>
                                <div class="col-sm-18">
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
                            <div class="row mb-4">
                                <label for="form-title" class="col-sm-6 col-form-label form-label">Title</label>
                                <div class="col-sm-18">
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
                            <div class="row mb-4">
                                <label for="form-post" class="col-sm-6 col-form-label form-label">Post</label>
                                <div class="col-sm-18">
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
                            <div class="row mb-0">
                                <div class="col-24">
                                    <button type="submit" class="btn w-100 btn-outline-primary">
                                        <i class="bi bi-send-fill me-2"></i>
                                        Broadcast message
                                    </button>
                                </div>
                            </div>
                        </form>
                    @elseif ($tab === 'history')
                        <livewire:tables.broadcast-messages />
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
