<div>
    <div class="card shadow mb-4">
        <div class="card-header border-bottom">
            <h4 class="mb-0">Plasma bot</h4>
        </div>
        <div class="card-body">
            <div class="bg-secondary shadow rounded-3 mb-4 p-3">
                <div class="d-block d-md-flex justify-content-md-evenly mb-3">
                    <div class="text-start text-md-center mb-2 mb-md-0 order-0">
                        <span class="d-inline d-md-block fs-sm text-muted">Available QSR</span>
                        <span class="float-end float-md-none">{{ ($account->display_qsr_balance ?: '-') }}</span>
                    </div>
                    <div class="text-start text-md-center mb-2 mb-md-0 order-0">
                        <span class="d-inline d-md-block fs-sm text-muted">Fused QSR</span>
                        <span class="float-end float-md-none">{{ ($account->display_qsr_fused ?: '-') }}</span>
                    </div>
                </div>
                <div class="text-start text-md-center">
                    <div class="progress bg-dark" style="height: 4px">
                        <div
                            class="progress-bar bg-success"
                            role="progressbar"
                            aria-label="No"
                            style="width: {{ $percentageUsed }}%"
                            aria-valuenow="{{ $percentageUsed }}"
                            aria-valuemin="0"
                            aria-valuemax="100"
                        ></div>
                        <div
                            class="progress-bar bg-zenon-pink"
                            role="progressbar"
                            aria-label="Yes"
                            style="width: {{ $percentageAvailable }}%"
                            aria-valuenow="{{ $percentageAvailable }}"
                            aria-valuemin="0"
                            aria-valuemax="100"
                        ></div>
                    </div>
                </div>
            </div>
            <hr class="border-secondary my-4">
            @if ($account->qsr_balance > 2000000000)
                <div class="w-100" wire:loading.delay>
                    <x-alert
                        message="Processing request..."
                        type="info"
                        icon="arrow-repeat spin"
                        class="mb-0"
                    />
                </div>
                @if ($result === true)
                    <x-alert
                        message="Generating plasma..."
                        type="success"
                        icon="check-circle-fill"
                        class="mb-4"
                    />
                    <p>Plasma is being generated for <a class="fw-bold" href="{{ route('explorer.account', ['address' => $address]) }}">{{ $address }}</a> please wait a few minutes for it to arrive.</p>
                    <p>Your plasma will expire in {{ $expires }} after which you'll be able to fuse some more.</p>
                @else
                    @if($result === false)
                        @if ($message)
                            <x-alert
                                message="{{$message}}"
                                type="info"
                                icon="info-circle"
                                class="mb-4"
                            />
                        @else
                            <x-alert
                                message="Error fusing, please try again"
                                type="danger"
                                icon="exclamation-octagon"
                                class="mb-4"
                            />
                        @endif
                    @endif
                    <p class="mb-4">
                        Need some plasma to speed up a transaction? Enter an address below to temporarily fuse some QSR and generate plasma.
                    </p>
                    <form wire:loading.remove wire:submit.prevent="submit">
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
                            <label for="form-address" class="col-sm-6 col-form-label form-label">Plasma</label>
                            <div class="col-sm-18 mt-2">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input @error('plasma')is-invalid @enderror" type="radio" id="plasma-low" name="plasma" value="low" wire:model="plasma" checked>
                                    <label class="form-check-label" for="plasma-low">Low</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input @error('plasma')is-invalid @enderror" type="radio" id="plasma-medium" name="plasma" value="medium" wire:model="plasma">
                                    <label class="form-check-label" for="plasma-medium">Medium</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input @error('plasma')is-invalid @enderror" type="radio" id="plasma-high" name="plasma" value="high" wire:model="plasma">
                                    <label class="form-check-label" for="plasma-high">High</label>
                                </div>
                            </div>
                        </div>
                        <div class="row mb-0">
                            <div class="col-24">
                                <button type="submit" class="btn w-100 btn-outline-primary">
                                    <i class="bi bi-fire me-2"></i>
                                    Get plasma
                                </button>
                            </div>
                        </div>
                    </form>
                @endif
            @else
                <x-alert
                    message="The bot has run out of QSR for the next {{ $nextExpiring?->expires_at->diffForHumans(['parts' => 2], true) }}, please check back later"
                    type="info"
                    icon="info-circle"
                    class="mb-0"
                />
            @endif
        </div>
    </div>
</div>
