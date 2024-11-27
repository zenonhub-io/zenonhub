@props(['tooltip' => null, 'class' => null, 'style' => null])

@if($tooltip)
    <span data-bs-toggle="tooltip" data-bs-title="{{ $tooltip }}">
@endif
    <x-svg :file="$svg" class="{{ $class ?? null }}" style="{{ $style ?? null }} "/>
@if($tooltip)
    </span>
@endif
