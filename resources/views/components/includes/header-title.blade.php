@props(['title' => null])

<div {{ $attributes->merge(['class' => 'd-flex align-items-center']) }}>
    @if ($slot->isEmpty())
        <h1 class="ls-tight text-wrap text-break">{{ $title }}</h1>
    @else
        {{ $slot }}
    @endif
</div>
