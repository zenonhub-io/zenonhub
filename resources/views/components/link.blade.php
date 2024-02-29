@props(['href'])

<a href="{{ $href }}" wire:navigate {{ $attributes }}>
    {{ $slot }}
</a>
