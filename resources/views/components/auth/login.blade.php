<div class="card shadow mb-4">
    <div class="card-header">
        <div class="text-center">
            <h2>Welcome back</h2>
            <p class="mb-0">Login to your account.</p>
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
        <form action="{{ route('login') }}" method="post" class="needs-validation">
            @csrf
            <input type="hidden" name="redirect" value="{{ old('redirect', request()->input('redirect')) }}">
            <div class="mb-4">
                <label for="form-email" class="form-label">Email</label>
                <input
                    type="email"
                    id="form-email"
                    name="email"
                    class="form-control form-control-lg @error('email')is-invalid @enderror"
                    value="{{ old('email') }}"
                    tabindex="7"
                >
                <div class="invalid-feedback">
                    @error('email') {{ $message }} @else Invalid email or username @enderror
                </div>
            </div>
            <div class="mb-4">
                <div class="d-flex justify-content-between align-items-center">
                    <label for="form-password" class="form-label">Password</label>
                    <a class="form-label-link" href="{{ route('password.request') }}" tabindex="9">Forgot Password?</a>
                </div>
                <input
                    type="password"
                    id="form-password"
                    name="password"
                    class="form-control form-control-lg @error('password')is-invalid @enderror"
                    tabindex="8"
                >
                <div class="invalid-feedback">
                    Invalid password
                </div>
            </div>
            <div class="mb-4">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="remember" value="remember" id="form-remember">
                    <label class="form-check-label" for="form-remember">
                        Remember me
                    </label>
                </div>
            </div>
            <div class="mb-3">
                <button type="submit" class="btn btn-primary btn-lg w-100">
                    <i class="bi bi-unlock me-2"></i>
                    Log in
                </button>
            </div>
            <div class="text-center">
                <p class="pb-0 mb-0">
                    Don't have an account yet? <a class="link" href="{{ route('sign-up') }}">Sign up</a>
                </p>
            </div>
        </form>
    </div>
</div>
