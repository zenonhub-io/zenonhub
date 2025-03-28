<div class="d-flex gap-1 text-xs">
    <span class="text-heading fw-semibold">Height:</span>
    <span class="text-muted"><livewire:utilities.current-height /></span>
</div>
<div class="d-flex gap-1 text-xs">
    <span class="text-heading fw-semibold">Bridge:</span>
    <span class="text-muted"><livewire:utilities.bridge-status /></span>
</div>

@if (! is_hqz())
    <div class="gap-1 text-xs d-none d-lg-flex">
        <span class="text-heading fw-semibold">{{ app('znnToken')->symbol }}:</span>
        <span class="text-primary">${{ number_format(app('znnToken')->price, 2) }}</span>
    </div>
    <div class="gap-1 text-xs d-none d-lg-flex">
        <span class="text-heading fw-semibold">{{ app('qsrToken')->symbol }}:</span>
        <span class="text-secondary">${{ number_format(app('qsrToken')->price, 2) }}</span>
    </div>
@endif
