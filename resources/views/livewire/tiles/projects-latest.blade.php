<div>
    <x-cards.card>
        <x-cards.heading class="d-flex align-items-center">
            <div class="title-icon me-3">
                <x-svg file="zenon/az" />
            </div>
            <h4 class="flex-grow-1 mb-0">
                {{ __('AZ Projects') }}
            </h4>
            <x-link :href="route('accelerator-z.list')" class="btn btn-xs btn-outline-primary">
                {{ __('All') }}
                <i class="bi bi-arrow-right ms-2"></i>
            </x-link>
        </x-cards.heading>

        @if ($projects->isEmpty())
            <x-alerts.alert type="info" class="m-6">
                <i class="bi bi-info-circle-fill me-2"></i> {{ __('No projects found') }}
            </x-alerts.alert>
        @else
            <ul class="list-group list-group-flush mb-0">
                @foreach ($projects as $project)
                    <li class="list-group-item d-flex align-items-start justify-content-between px-6 bg-body-secondary-hover">
                        <div class="d-block">
                            <x-link class="stretched-link" :href="route('accelerator-z.project.detail', ['hash' => $project->hash])">
                                {{ $project->name }}
                            </x-link>
                            <span class="text-xs d-block text-muted">{{ $project->phases()->count() }} {{ Str::plural('Phase', $project->phases()->count()) }}</span>
                        </div>
                        <div class="d-block text-end">
                            <span class="badge text-bg-{{ $project->status->colour() }} bg-opacity-75">{{ $project->status->label() }}</span>
                        </div>
                    </li>
                @endforeach
            </ul>
        @endif
    </x-cards.card>
</div>
