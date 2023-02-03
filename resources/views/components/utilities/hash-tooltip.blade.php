@if ($alwaysShort)
    <span data-bs-toggle="tooltip" data-bs-title="{{ $hash }}">
        {{ short_hash($hash, $eitherSide) }}
    </span>
@else
    <span class="d-inline d-{{ $breakpoint }}-none" data-bs-toggle="tooltip" data-bs-title="{{ $hash }}">
        {{ short_hash($hash, $eitherSide) }}
    </span>

    <span class="d-none d-{{ $breakpoint }}-inline">
        {{ $hash }}
    </span>
@endif
