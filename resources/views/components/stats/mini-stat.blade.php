@props(['title' => null, 'info' => null, 'stat' => null])

<div class="d-flex gap-3 mb-1">
    <span class="text-muted">{{ __($title) }}</span>
    @if($info)
        <span class="text-muted text-opacity-60 text-opacity-100-hover" tabindex="0" role="button"
              data-bs-toggle="popover"
              data-bs-trigger="hover focus"
              data-bs-placement="right"
              data-bs-html="true"
              data-bs-content="{{ $info }}">
            <i class="bi bi-info-circle"></i>
        </span>
    @endif
</div>
<div class="d-flex align-items-center">
    <span class="text-lg text-heading fw-bold">
        {{ $slot->isEmpty() ? $stat : $slot }}
    </span>
</div>
