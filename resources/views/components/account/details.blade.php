<div class="card shadow mb-4">
    <div class="card-header">
        <h4 class="mb-0">Update your details</h4>
    </div>
    <div class="card-body">
        @if (session('alert'))
            <x-alert
                message="{{ session('alert.message') }}"
                type="{{ session('alert.type') }}"
                icon="{{ session('alert.icon') }}"
                class="mb-4"
            />
        @endif
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
    </div>
</div>
