<div class="card shadow mb-4">
    <div class="card-header">
        <div class="text-center">
            <h2>Confirm your email</h2>
            <p class="mb-0">Check you email for a verification link.</p>
        </div>
    </div>
    <div class="card-body text-center">
        <p>If you did not receive an email click the button below to resend.</p>
        @if (session('alert'))
            <x-alert
                message="{{ session('alert.message') }}"
                type="{{ session('alert.type') }}"
                icon="{{ session('alert.icon') }}"
            />
        @endif
        <form action="{{ route('verification.send') }}" method="post">
            @csrf
            <button class="btn btn-primary btn-sm ms-3" type="submit">
                <i class="bi bi-send me-2"></i>
                Resend link
            </button>
        </form>
    </div>
</div>
