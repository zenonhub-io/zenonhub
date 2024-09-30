<x-link :href="route('accelerator-z.project.detail', ['hash' => $row->votable->hash])">
    {{ $row->pillar->name }}
</x-link>
