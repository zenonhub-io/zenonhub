@props(['type' => 'button', 'text'])

<button {{ $attributes->merge(['class' => 'btn']) }} type="{{ $type }}">
    {{ $slot->isEmpty() ? $text : $slot }}
</button>
