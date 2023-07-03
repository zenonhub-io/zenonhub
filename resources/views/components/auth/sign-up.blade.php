<div class="card shadow mb-4">
    <div class="card-header">
        <div class="text-center">
            <h2>Create your account</h2>
            <p class="mb-0">
                Already have an account? <a class="link" href="{{ route('login') }}">Sign in</a>
            </p>
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
        <form action="{{ route('sign-up') }}" method="post" class="needs-validation">
            @csrf
            <div class="mb-4">
                <label for="form-username" class="form-label">Username</label>
                <input
                    type="text"
                    id="form-username"
                    name="username"
                    class="form-control form-control-lg @error('username')is-invalid @enderror"
                    value="{{ old('username') }}"
                >
                <div class="invalid-feedback">
                    @error('username') {{ $message }} @enderror
                </div>
            </div>
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
                <div class="row">
                    <div class="col">
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
                    <div class="col">
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
                </div>
            </div>
            <div class="mb-4">
                <div class="form-check">
                    <input
                        type="checkbox"
                        id="form-terms"
                        name="terms"
                        class="form-check-input @error('password_confirmation')is-invalid @enderror"
                        value="yes"
                    >
                    <label class="form-check-label" for="form-terms">
                        By submitting this form I have read and acknowledged the <a href="{{ route('privacy') }}" target="_blank">Privacy Policy</a>
                    </label>
                    <div class="invalid-feedback">
                        @error('terms') {{ $message }} @enderror
                    </div>
                </div>
            </div>
            <button class="btn btn-primary btn-lg w-100" type="submit">
                <i class="bi bi-person-plus me-2"></i>
                Create account
            </button>
        </form>
    </div>
</div>
