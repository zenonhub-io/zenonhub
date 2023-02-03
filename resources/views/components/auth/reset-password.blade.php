<div class="card shadow mb-4">
    <div class="card-header">
        <div class="text-center">
            <h2>Forgot your password?</h2>
            <p class="mb-0">To reset your password simply complete the form below.</p>
        </div>
    </div>
    <div class="card-body">
        @if (session('alert'))
            <x-alert
                message="{{ session('alert.message') }}"
                type="{{ session('alert.type') }}"
                icon="{{ session('alert.icon') }}"
            />
        @endif
        <form action="{{ route('password.update') }}" method="post" class="needs-validation">
            @csrf
            <input
                type="hidden"
                id="form-token"
                name="token"
                value="{{ $data['token'] }}"
            >
            <div class="mb-4">
                <label for="form-email" class="form-label">Email address</label>
                <input
                    type="email"
                    id="form-email"
                    name="email"
                    class="form-control form-control-lg @error('email')is-invalid @enderror"
                    value="{{ old('email') }}"
                >
                <div class="invalid-feedback">
                    @error('email') {{ $message }} @enderror
                </div>
            </div>
            <div class="mb-4">
                <label for="form-password" class="form-label">Password</label>
                <input
                    type="password"
                    id="form-password"
                    name="password"
                    class="form-control form-control-lg @error('password')is-invalid @enderror"
                >
                <div class="invalid-feedback">
                    @error('password') {{ $message }} @enderror
                </div>
            </div>
            <div class="mb-4">
                <label for="form-password-confirmation" class="form-label">Confirm Password</label>
                <input
                    type="password"
                    id="form-password-confirmation"
                    name="password_confirmation"
                    class="form-control form-control-lg @error('password_confirmation')is-invalid @enderror"
                >
                <div class="invalid-feedback">
                    @error('password') {{ $message }} @enderror
                </div>
            </div>
            <button class="w-100 btn btn-primary" type="submit">
                <i class="bi bi-check-circle me-2"></i>
                Change password
            </button>
        </form>
    </div>
</div>
