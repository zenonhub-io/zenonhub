<div>
    <div class="card shadow mb-4">
        <div class="card-header">
            <h4 class="mb-0">Mange your account</h4>
        </div>
        <div class="card-header">
            <div class="d-md-none">
                <select id="sections" class="form-control" wire:change="$emit('tabChange', $event.target.value)">
                    <option value="funding" {{ $tab === 'details' ? 'selected' : '' }}>Details</option>
                    <option value="projects" {{ $tab === 'password' ? 'selected' : '' }}>Password</option>
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
                    @if ($tab === 'details')
                        <form action="{{ route('account.details') }}" method="post" class="needs-validation">
                            @csrf
                            <div class="row mb-4">
                                <label for="form-username" class="col-sm-6 col-form-label form-label">Username</label>
                                <div class="col-sm-18">
                                    <input
                                        type="text"
                                        id="form-username"
                                        name="username"
                                        class="form-control @error('username')is-invalid @enderror"
                                        value="{{ old('username', auth()->user()->username) }}"
                                    >
                                    <div class="invalid-feedback">
                                        @error('username') {{ $message }} @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-4">
                                <label for="form-email" class="col-sm-6 col-form-label form-label">Email address</label>
                                <div class="col-sm-18">
                                    <input
                                        type="email"
                                        id="form-email"
                                        name="email"
                                        class="form-control @error('email')is-invalid @enderror"
                                        value="{{ old('email', auth()->user()->email) }}"
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
                        <form action="{{ route('account.lists') }}" method="post" class="needs-validation">
                            @csrf
                            <div class="row mb-4">
                                <label for="form-current-password" class="col-sm-6 col-form-label form-label">Password</label>
                                <div class="col-sm-18">
                                    <input
                                        type="password"
                                        id="form-current-password"
                                        name="current_password"
                                        class="form-control @error('current_password')is-invalid @enderror"
                                    >
                                    <div class="invalid-feedback">
                                        @error('current_password') {{ $message }} @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-4">
                                <label for="form-password" class="col-sm-6 col-form-label form-label">New password</label>
                                <div class="col-sm-18">
                                    <input
                                        type="password"
                                        id="form-password"
                                        name="password"
                                        class="form-control @error('password')is-invalid @enderror"
                                    >
                                    <div class="invalid-feedback">
                                        @error('password') {{ $message }} @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-4">
                                <label for="form-confirm-password" class="col-sm-6 col-form-label form-label">Confirm</label>
                                <div class="col-sm-18">
                                    <input
                                        type="password"
                                        id="form-confirm-password"
                                        name="password_confirmation"
                                        class="form-control @error('password_confirmation')is-invalid @enderror"
                                    >
                                    <div class="invalid-feedback">
                                        @error('password_confirmation') {{ $message }} @enderror
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
