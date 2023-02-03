<span class="me-2">
    {{ $title }}
</span>

@if ($tooltip)
    <span class="text-muted me-2"><i class="bi-question-circle" data-bs-toggle="tooltip" data-bs-title="{{ $tooltip }}"></i></span>
@endif

@if ($sort === $check)
    @if ($order === 'desc')
        <i class="bi bi-chevron-down opacity-75"></i>
    @else
        <i class="bi bi-chevron-up opacity-75"></i>
    @endif
@else
    <i class="bi bi-chevron-down opacity-25"></i>
@endif
