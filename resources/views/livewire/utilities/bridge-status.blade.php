<div wire:poll.10s>
    <x-link :href="route('stats.bridge')" class="text-muted"
            data-bs-toggle="tooltip" data-bs-title="{{ $message }}"
    >
        <span class="me-1">Online</span> <x-stats.indicator :type="$indicator" />
    </x-link>
</div>
