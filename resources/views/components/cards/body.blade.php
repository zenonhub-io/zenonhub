@props(['body'])

<div {{ $attributes->merge(['class' => "card-body"]) }}>
    {{ $slot->isEmpty() ? $body : $slot }}
</div>
