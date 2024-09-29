<div class="container-fluid px-3 px-md-6">
    <div class="row g-5">
        @foreach($projects as $project)
            <div class="col-24 col-lg-12">
                <x-cards.card class="h-100">
                    <x-slot:heading>
                        <div class="d-flex align-items-center mb-0">
                            <h4 class="card-title me-3 mb-0">
                                <x-link class="h4" :href="route('accelerator-z.project.detail', ['hash' => $project->hash])">
                                    {{ $project->name }}
                                </x-link>
                            </h4>
                            <div class="me-auto pe-3">
                                <x-link :href="$project->url" target="_blank" class="link-light">
                                    <i class="bi bi-box-arrow-up-right"></i>
                                </x-link>
                            </div>
                            <div>
                                <span class="badge text-bg-{{ $project->status->colour() }} bg-opacity-75">{{ $project->status->label() }}</span>
                            </div>
                        </div>
                    </x-slot>
                    <x-slot:body>
                        <x-accelerator-z.funding-card :item="$project"/>
                        <p>{{ $project->description }}</p>
                    </x-slot:body>

                    @if ($project->phases->count())
                        <ul class="list-group list-group-flush mb-0" id="phases-{{ $project->hash }}">
                            @foreach ($project->phases as $phase)
                                <li class="list-group-item px-4 py-3">
                                    <div class="accordion-button collapsed" role="button"
                                         data-bs-toggle="collapse" data-bs-target="#phase-collapse-{{ $phase->hash }}"
                                         aria-expanded="false" aria-controls="phase-collapse-{{ $phase->hash }}"
                                    >
                                        <div class="d-flex align-items-center w-100">
                                            <div class="me-auto mb-0">
                                                <div class="text-muted text-xs">
                                                    Phase {{ $phase->phase_number }}
                                                </div>
                                                {{ $phase->name }}
                                            </div>
                                            <div class="ps-3">
                                                <span class="badge text-bg-{{ $phase->status->colour() }} bg-opacity-75">{{ $phase->status->label() }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div id="phase-collapse-{{ $phase->hash }}" class="accordion-collapse collapse"
                                         data-bs-parent="#phases-{{ $project->hash }}"
                                    >
                                        <div class="mt-4">
                                            <x-accelerator-z.funding-card :item="$phase"/>
                                            <p class="mb-0">
                                                {{ $phase->description }}
                                            </p>
                                        </div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </x-cards.card>
            </div>
        @endforeach
    </div>
</div>
