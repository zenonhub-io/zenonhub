@props(['title' => null, 'info' => null, 'stat' => null])

<div class="d-flex align-items-center gap-3 mb-2">
    <h5 class="text-muted">{{ __($title) }}</h5>
    @if($info)
        <span class="text-muted text-opacity-60 text-opacity-100-hover" tabindex="0" role="button"
              data-bs-toggle="tooltip" data-bs-title="{{ $info }}">
            <i class="bi bi-info-circle"></i>
        </span>
    @endif
</div>
<div class="d-flex align-items-center">
    <span class="text-lg text-heading fw-semibold text-nowrap">
        {{ $slot->isEmpty() ? $stat : $slot }}
    </span>
</div>
