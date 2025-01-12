@props(['showImage' => false])

<x-link class="text-nowrap" :href="route('pillar.detail', ['slug' => $pillar->slug])">
    @if ($showImage && $pillar->socialProfile?->avatar)
        <div class="title-icon d-inline me-1">
            <img src="{{ $pillar->socialProfile?->avatar }}" class="rounded d-inline" alt="{{ $pillar->name }} Logo"/>
        </div>
    @endif
    {{ $pillar->name }}
</x-link>
