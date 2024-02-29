@props(['content'])

<div class="modal-footer">
    {{ $slot->isEmpty() ? $content : $slot }}
</div>
