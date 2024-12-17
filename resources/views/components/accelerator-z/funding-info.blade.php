@props(['item'])

<div {{ $attributes->merge(['class' => "d-block d-md-flex justify-content-md-evenly mb-4"]) }}>
    <div class="text-start text-md-center mb-2 mb-md-0">
        <span class="d-inline d-md-block text-muted text-sm">ZNN</span>
        <span class="float-end float-md-none text-primary">{{ $item->display_znn_requested }}</span>
    </div>
    <div class="text-start text-md-center mb-2 mb-md-0">
        <span class="d-inline d-md-block text-muted text-sm">QSR</span>
        <span class="float-end float-md-none text-secondary">{{ $item->display_qsr_requested }}</span>
    </div>
    <div class="text-start text-md-center">
        <span class="d-inline d-md-block text-muted text-sm">USD</span>
        <span class="float-end float-md-none">{{ $item->display_usd_requested }}</span>
    </div>
</div>
