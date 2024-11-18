@props(['title'])

<div {{ $attributes->merge(['class' => "card-header fw-bold"]) }}>
    {{ $slot->isEmpty() ? $title : $slot }}
</div>
