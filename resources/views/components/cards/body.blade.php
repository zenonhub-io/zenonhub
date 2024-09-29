@props(['body'])

<div class="card-body">
    {{ $slot->isEmpty() ? $body : $slot }}
</div>
