@props(['rounded' => true])

<div class="input-group input-group-inline {{ $rounded ? 'rounded-pill' : null }}">
    <span class="input-group-text {{ $rounded ? 'rounded-start-pill' : null }}">
        <i class="bi bi-search me-2"></i>
    </span>
    <input type="search" class="form-control ps-0 {{ $rounded ? 'rounded-end-pill' : null }}" placeholder="Search" aria-label="Search">
</div>
