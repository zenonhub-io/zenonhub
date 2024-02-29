@props([
	'id' => false,
    'trigger' => false,
    'heading',
    'footer'
])

@php
    if (! $id) {
        $id = Str::random(8);
    }
@endphp

@if ($trigger)
    <button type="button"
            {{ $trigger->attributes->merge(['class' => 'btn']) }}
            data-bs-toggle="modal" data-bs-target="#{{ $id }}"
    >
        {{ $trigger }}
    </button>
@endif

<div id="{{ $id }}" tabindex="-1"
     class="modal fade"
     data-bs-backdrop="static"
     aria-labelledby="exampleModalLabel" aria-hidden="true"
>
    <div {{ $attributes->merge(['class' => 'modal-dialog']) }}>
        <div class="modal-content">

            @isset($heading)
                <x-modals.heading :title="$heading" />
            @endisset

            <div class="modal-body">
                {{ $slot }}
            </div>

            @isset($footer)
                <x-modals.footer :content="$footer" />
            @endisset
        </div>
    </div>
</div>
