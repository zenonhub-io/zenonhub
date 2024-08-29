@props(['href', 'navigate' => true])

<a href="{{ $href }}" {{ $navigate ? 'wire:navigate' : '' }} {{ $attributes }}>
    {{ $slot }}
</a>
