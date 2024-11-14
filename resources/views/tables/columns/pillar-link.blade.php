<x-link :href="route('pillar.detail', ['slug' => $row->slug])">
    {{ $row->name }}
</x-link>
