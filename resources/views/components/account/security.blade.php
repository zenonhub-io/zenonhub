<div class="card shadow mb-4">
    <div class="card-header">
        <h4 class="mb-0">Change your password</h4>
    </div>
    @if (session('alert'))
        <x-alert
            message="{{ session('alert.message') }}"
            type="{{ session('alert.type') }}"
            icon="{{ session('alert.icon') }}"
            class="d-flex align-items-center"
        />
    @endif
    <div class="card-body">
        <form action="{{ route('account.security') }}" method="post" class="needs-validation">
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
            <button class="w-100 btn btn-primary" type="submit">
                <i class="bi bi-check-circle me-2"></i>
                Change password
            </button>
        </form>
    </div>
</div>
