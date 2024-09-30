@if ($row->votable instanceof \App\Models\Nom\AcceleratorProject)
    <div class="text-muted fs-xs mb-n1">
        Project
    </div>
    <x-link :href="route('accelerator-z.project.detail', ['hash' => $row->votable->hash])">
        {{ $row->votable->name }}
    </x-link>
@endif
@if ($row->votable instanceof \App\Models\Nom\AcceleratorPhase)
    <div class="text-muted fs-xs mb-n1">
        {{ $row->votable->load('project')->project->name }}
    </div>
    <x-link :href="route('accelerator-z.phase.detail', ['hash' => $row->votable->hash])">
        {{ $row->votable->name }}
    </x-link>
@endif
