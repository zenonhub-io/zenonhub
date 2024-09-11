@props(['title' => null, 'stat' => null, 'info' => null, 'hr' => true])

<div class="d-block d-xl-flex align-items-center">
    <h5 class="mb-1 mb-xl-0 me-2 d-inline-flex text-muted">{{ $title }}</h5>
    @if($info)
        <span class="text-muted text-opacity-60 text-opacity-100-hover" tabindex="0" role="button"
              data-bs-toggle="tooltip" data-bs-title="{{ $info }}">
            <i class="bi bi-info-circle"></i>
        </span>
    @endif

    <div class="ms-auto text-nowrap fw-semibold">
        {{ $slot->isEmpty() ? $stat : $slot }}
    </div>
</div>

@if ($hr)
    <hr class="my-0">
@endif
