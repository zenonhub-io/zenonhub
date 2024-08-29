@props(['title' => null, 'stat' => null, 'hr' => true])

<div class="d-flex align-items-center">
    <h6 class="fw-semibold">{{ $title }}</h6>
    <div class="ms-auto">
        {{ $slot->isEmpty() ? $stat : $slot }}
    </div>
</div>

@if ($hr)
    <hr class="my-0">
@endif
