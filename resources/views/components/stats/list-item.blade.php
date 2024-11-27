@props(['title' => null, 'stat' => null, 'info' => null, 'hr' => true, 'breakpoint' => 'lg'])

<div class="d-block d-{{ $breakpoint }}-flex align-items-center">
    <h6 class="mb-1 mb-{{ $breakpoint }}-0 me-2 d-inline-flex text-muted">{{ $title }}</h6>
    @if($info)
        <span class="text-muted text-opacity-60 text-opacity-100-hover" tabindex="0" role="button"
              data-bs-toggle="tooltip" data-bs-title="{{ $info }}">
            <i class="bi bi-info-circle"></i>
        </span>
    @endif

    <div class="ms-auto text-wrap overflow-hidden">
        {{ $slot->isEmpty() ? $stat : $slot }}
    </div>
</div>

@if ($hr)
    <hr class="my-0">
@endif
