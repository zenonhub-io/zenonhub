<div>
    <div class="card shadow mb-4">
        <div class="card-header border-bottom">
            <h4 class="mb-0">Plasma bot</h4>
        </div>
        <div class="card-body">
            <p>
                Need some plasma to speed up a transaction? Enter an address below to temporarily fuse some QSR and generate some plasma.
            </p>
            <div class="bg-secondary shadow rounded-3 mt-3 mb-3 p-3">
                <div class="d-flex justify-content-evenly mb-3">
                    <span class="badge bg-secondary text-muted">
                        {{ $account->display_qsr_balance }} Available QSR
                    </span>
                    <span class="badge bg-secondary text-muted">
                        {{ $account->display_qsr_fused }} Fused QSR
                    </span>
                </div>
                <div class="text-start text-md-center">
                    <div class="progress bg-dark" style="height: 4px">
                        <div
                            class="progress-bar bg-danger"
                            role="progressbar"
                            aria-label="Yes"
                            style="width: {{ $percentageAvailable }}%"
                            aria-valuenow="{{ $percentageAvailable }}"
                            aria-valuemin="0"
                            aria-valuemax="100"
                        ></div>
                        <div
                            class="progress-bar bg-success"
                            role="progressbar"
                            aria-label="No"
                            style="width: {{ $percentageUsed }}%"
                            aria-valuenow="{{ $percentageUsed }}"
                            aria-valuemin="0"
                            aria-valuemax="100"
                        ></div>
                    </div>
                </div>
            </div>
            <hr class="border-secondary my-4">
            @if ($result === true)
                <x-alert
                    message="Plasma fused"
                    type="success"
                    icon="check-circle-fill"
                    class="mb-3"
                />
            @elseif ($result === false)
                <x-alert
                    message="Error fusing, please try again"
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
                    <label for="form-address" class="col-sm-6 col-form-label form-label">Plasma</label>
                    <div class="col-sm-18">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input @error('plasma')is-invalid @enderror" type="radio" id="plasma-low" name="plasma" value="low" wire:model="plasma">
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
        </div>
    </div>
</div>
