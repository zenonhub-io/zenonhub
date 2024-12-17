@if ($exists())
    <div role="alert" {{ $attributes->merge(['class' => "alert alert-{$class()} shadow"]) }}>
        {{ $slot->isEmpty() ? $message() : $slot }}
    </div>
@endif
