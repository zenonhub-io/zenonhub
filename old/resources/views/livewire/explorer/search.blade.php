<div>
    <div class="card shadow border-0 mb-4">
        <label for="explorer.filters.search" class="visually-hidden form-label">Search hash / height / address / token</label>
        <div class="input-group input-group-merge input-group-lg">
            <span class="input-group-prepend input-group-text">
                <i class="bi-search"></i>
            </span>
            <input
                type="text"
                class="form-control"
                id="explorer.filters.search"
                placeholder="Search hash / height / address / token"
                aria-label="Search hash / height / address / token"
                wire:model.debounce.500ms="search"
                value="{{ $search }}"
            >
        </div>
    </div>
</div>
