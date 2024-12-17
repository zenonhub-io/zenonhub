@props(['href', 'navigate' => true, 'newTab' => false])

<a href="{{ $href }}" {{ $navigate ? 'wire:navigate' : '' }} {{ $newTab ? 'target="_blank"' : '' }}  {{ $attributes }}>
    {{ $slot }}
</a>
