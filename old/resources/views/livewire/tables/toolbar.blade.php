<div>
    <div class="d-flex">
        <div class="flex-grow-1">
            <div class="input-group input-group-merge">
                <span class="input-group-prepend input-group-text">
                    <i class="bi-search"></i>
                </span>
                <input
                    type="text"
                    class="form-control"
                    placeholder="Search"
                    aria-label="Search"
                    wire:keydown.debounce.300ms="$emit('search', $event.target.value)"
                    value="{{ $search }}"
                >
            </div>
        </div>
        @if ($enableExport)
            <div class="ms-2">
                <button type="button" class="btn btn-outline-secondary" wire:click="$emit('export')">
                    <i class="bi bi-download"></i>
                </button>
            </div>
        @endif
    </div>
</div>
