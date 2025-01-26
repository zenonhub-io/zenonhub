@props(['end' => false])

@php($uuid = Str::random(8))

<button type="button"
        {{ $trigger->attributes }}
        data-bs-toggle="offcanvas" data-bs-target="#{{ $uuid }}"
>
    {{ $trigger }}
</button>

<div class="offcanvas {{ $end ? 'offcanvas-end' : 'offcanvas-start' }}" tabindex="-1" id="{{ $uuid }}" aria-labelledby="offcanvasLabel">
    @isset($heading)
        <x-offcanvas.heading :title="$heading" />
    @endisset
    <div class="offcanvas-body">
        {{ $slot }}
    </div>
</div>
