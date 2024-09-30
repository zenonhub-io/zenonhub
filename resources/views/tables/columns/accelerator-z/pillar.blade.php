<x-link :href="route('pillar.detail', ['slug' => $row->pillar->slug])">
    {{ $row->pillar->name }}
</x-link>
