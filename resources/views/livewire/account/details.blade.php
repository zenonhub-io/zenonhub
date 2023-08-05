<div>
    <div class="card shadow mb-4">
        <div class="card-header">
            <h4 class="mb-0">Update your account</h4>
        </div>
        <div class="card-header">
            <div class="d-md-none">
                <select id="sections" class="form-select" wire:change="$emit('tabChange', $event.target.value)">
                    <option value="details" {{ $tab === 'details' ? 'selected' : '' }}>Details</option>
                    <option value="password" {{ $tab === 'password' ? 'selected' : '' }}>Password</option>
                </select>
            </div>
            <div class="d-none d-md-block">
                <ul class="nav nav-tabs-alt card-header-tabs">
                    <li class="nav-item">
                        <button class="btn nav-link {{ $tab === 'details' ? 'active' : '' }}" wire:click="$emit('tabChange', 'details')">
                            Details
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="btn nav-link {{ $tab === 'password' ? 'active' : '' }}" wire:click="$emit('tabChange', 'password')">
                            Password
                        </button>
                    </li>
                </ul>
            </div>
        </div>
        <div class="card-body">
            <div class="tab-content">
                <div class="tab-pane show active">
                    @if($result)
                        <x-alert
                            message="Account details updated"
                            type="success"
                            icon="check-circle-fill"
                            class="mb-4"
                        />
                    @endif
                    @if ($tab === 'details')
                        <form wire:submit.prevent="onUpdateDetails" class="needs-validation">
                            <div class="row mb-4 align-items-center">
                                <label for="form-username" class="form-label col-md-6">Username</label>
                                <div class="col-md-18">
                                    <input
                                        type="text"
                                        id="form-username"
                                        wire:model="username"
                                        class="form-control @error('username')is-invalid @enderror"
                                    >
                                    <div class="invalid-feedback">
                                        @error('username') {{ $message }} @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-4 align-items-center">
                                <label for="form-email" class="form-label col-md-6">Email</label>
                                <div class="col-md-18">
                                    <input
                                        type="email"
                                        id="form-email"
                                        wire:model="email"
                                        class="form-control @error('email')is-invalid @enderror"
                                    >
                                    <div class="invalid-feedback">
                                        @error('email') {{ $message }} @enderror
                                    </div>
                                </div>
                            </div>
                            <button class="w-100 btn btn-outline-primary" type="submit">
                                <i class="bi bi-person-check me-2"></i>
                                Save details
                            </button>
                        </form>
                    @endif

                    @if ($tab === 'password')
                        <form wire:submit.prevent="onChangePassword" class="needs-validation">
                            <div class="row mb-4 align-items-center">
                                <label for="form-old-password" class="form-label col-md-6">Old password</label>
                                <div class="col-md-18">
                                    <input
                                        type="password"
                                        id="form-old-password"
                                        wire:model="old_password"
                                        class="form-control @error('old_password')is-invalid @enderror"
                                    >
                                    <div class="invalid-feedback">
                                        @error('old_password') {{ $message }} @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-4 align-items-center">
                                <label for="form-new-password" class="form-label col-md-6">New password</label>
                                <div class="col-md-18">
                                    <input
                                        type="password"
                                        id="form-new-password"
                                        wire:model="new_password"
                                        class="form-control @error('new_password')is-invalid @enderror"
                                    >
                                    <div class="invalid-feedback">
                                        @error('new_password') {{ $message }} @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-4 align-items-center">
                                <label for="form-confirm-new-password" class="form-label col-md-6">Confirm</label>
                                <div class="col-md-18">
                                    <input
                                        type="password"
                                        id="form-confirm-new-password"
                                        wire:model="new_password_confirmation"
                                        class="form-control @error('new_password_confirmation')is-invalid @enderror"
                                    >
                                    <div class="invalid-feedback">
                                        @error('new_password_confirmation') {{ $message }} @enderror
                                    </div>
                                </div>
                            </div>
                            <button class="w-100 btn btn-outline-primary" type="submit">
                                <i class="bi bi-check-circle me-2"></i>
                                Change password
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
