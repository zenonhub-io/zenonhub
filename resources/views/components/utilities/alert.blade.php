<div role="alert" {{ $attributes->merge(['class' => 'alert d-flex align-items-center alert-'.($type ?: 'info') . ($closeButton ? ' alert-dismissible fade show' : '') ]) }}>
    @if ($icon)
        <i class="bi bi-{{ $icon }} lead me-3"></i>
    @endif
    <div>
        {!! $message !!}
        {{ $slot }}
    </div>
    @if ($closeButton)
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    @endif
</div>
