<x-layouts.app>
    <x-slot name="breadcrumbs">
        {{ Breadcrumbs::render('project', $project) }}
    </x-slot>
    <div class="container">
        <div class="row">
            <div class="col-24">
                <livewire:az.project :hash="$project->hash"/>
            </div>
        </div>
    </div>
</x-layouts.app>
