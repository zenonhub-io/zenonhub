<div>
    <x-cards.card>
        <x-cards.heading class="d-flex align-items-center">
            <h4 class="flex-grow-1 mb-0">
                {{ __('Latest projects') }}
            </h4>
            <x-link :href="route('accelerator-z.list')" class="btn btn-xs btn-outline-primary">
                {{ __('All') }}
                <i class="bi bi-arrow-right ms-2"></i>
            </x-link>
        </x-cards.heading>
        <ul class="list-group list-group-flush mb-0">
            @foreach ($projects as $project)
                <li class="list-group-item d-flex align-items-start justify-content-between px-4">
                    <div class="d-block">
                        <x-link :href="route('accelerator-z.project.detail', ['hash' => $project->hash])">
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
    </x-cards.card>
</div>
