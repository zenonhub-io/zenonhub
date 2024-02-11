<div class="card shadow mb-4">
    <div class="card-header">
        <div class="text-center">
            <h2>Forgot your password?</h2>
            <p class="mb-0">Enter your email address below to get a new one.</p>
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
        <form action="{{ route('password.request') }}" method="post" class="needs-validation">
            @csrf
            <div class="mb-4">
                <div class="d-flex justify-content-between align-items-center">
                    <label for="form-email" class="form-label">Your email</label>
                    <a class="form-label-link" href="{{ route('login') }}">
                        <i class="bi-chevron-left small ms-1"></i> Back to Log in
                    </a>
                </div>
                <input
                    type="email"
                    id="form-email"
                    name="email"
                    class="form-control form-control-lg @error('email')is-invalid @enderror"
                    placeholder="name@example.com"
                    value="{{ old('email') }}"
                >
                <div class="invalid-feedback">
                    @error('email') {{ $message }} @else Invalid email address @enderror
                </div>
            </div>
            <button class="btn btn-primary btn-lg w-100" type="submit">
                <i class="bi bi-send me-2"></i>
                Send link
            </button>
        </form>
    </div>
</div>
