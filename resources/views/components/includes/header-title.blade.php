@props(['title' => null, 'centered' => false])

<div {{ $attributes->merge(['class' => 'd-flex align-items-center ' . $centered ? 'justify-content-center' : null]) }}>
    @if ($slot->isEmpty())
        <h1 class="ls-tight text-wrap text-break {{ $centered ? 'text-center' : null }}">{{ $title }}</h1>
    @else
        {{ $slot }}
    @endif
</div>
