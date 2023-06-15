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

    </div>
</div>
