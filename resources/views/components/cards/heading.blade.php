@props(['title'])

<div class="card-header fw-bold">
    {{ $slot->isEmpty() ? $title : $slot }}
</div>
