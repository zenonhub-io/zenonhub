@props([
    'hash',
    'link' => null,
    'alwaysShort' => false,
    'alwaysLong' => false,
    'eitherSide' => 10,
    'breakpoint' => 'md',
    'copyable' => false
])

@if($link)
    <x-link :href="$link">
        @if ($alwaysShort)
            <span data-bs-toggle="tooltip" data-bs-title="{{ $hash }}">
                {{ short_hash($hash, $eitherSide) }}
            </span>
        @elseif ($alwaysLong)
            {{ $hash }}
        @else
            <span class="d-inline d-{{ $breakpoint }}-none" data-bs-toggle="tooltip" data-bs-title="{{ $hash }}">
                {{ short_hash($hash, $eitherSide) }}
            </span>
            <span class="d-none d-{{ $breakpoint }}-inline">
                {{ $hash }}
            </span>
        @endif
    </x-link>
@else
    @if ($alwaysShort)
        <span data-bs-toggle="tooltip" data-bs-title="{{ $hash }}">
            {{ short_hash($hash, $eitherSide) }}
        </span>
    @elseif ($alwaysLong)
        {{ $hash }}
    @else
        <span class="d-inline d-{{ $breakpoint }}-none" data-bs-toggle="tooltip" data-bs-title="{{ $hash }}">
            {{ short_hash($hash, $eitherSide) }}
        </span>
        <span class="d-none d-{{ $breakpoint }}-inline">
            {{ $hash }}
        </span>
    @endif
@endif

@if($copyable)
    <x-copy class="ms-1" :text="$hash" />
@endif
