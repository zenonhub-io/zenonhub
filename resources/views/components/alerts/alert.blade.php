@props(['message', 'type' => 'info'])

<div role="alert" {{ $attributes->merge(['class' => "alert alert-{$type} shadow"]) }}>
    {{ $slot->isEmpty() ? $message : $slot }}
</div>
