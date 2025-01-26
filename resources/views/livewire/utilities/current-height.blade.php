<div wire:poll.10s>
    <x-link :href="route('explorer.momentum.list')" class="text-muted"
            data-bs-toggle="tooltip" data-bs-title="{{ $message }}"
    >
        <span class="me-1">{{ number_format($height) }}</span> <x-stats.indicator :type="$status ? 'success' : 'warning'" />
    </x-link>
</div>
