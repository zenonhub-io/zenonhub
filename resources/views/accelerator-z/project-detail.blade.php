<x-app-layout>
    <x-includes.header :responsive-border="false">
        <div class="d-flex justify-content-between mb-4">
            <div class="d-flex align-items-start flex-column">
                <div class="d-flex align-items-center mb-1">
                    <div class="title-icon">
                        <x-svg file="zenon/az" />
                    </div>
                    <h5 class="text-muted ms-3">{{ __('Project') }}</h5>
                </div>
                <x-includes.header-title>
                    <h1 class="ls-tight text-wrap text-break">
                        {{ $project->name }}
                        <x-copy :text="route('accelerator-z.project.detail', ['hash' => $project->hash])" class="ms-2 text-md" :tooltip="__('Copy URL')" />
                    </h1>
                </x-includes.header-title>
            </div>
            <div class="d-flex align-items-start">
                <span class="badge badge-md text-bg-{{ $project->status->colour() }}">{{ $project->status->label() }}</span>
            </div>
        </div>
    </x-includes.header>

    <div class="container-fluid px-3 px-md-6">

        <div class="row mb-6 gy-6">
            <div class="col-12 col-sm-8">
                <x-cards.card>
                    <x-cards.body>
                        <x-stats.mini-stat :title="__('ZNN Request')">
                                <span class="text-primary">
                                    {{ $project->display_znn_requested }}
                                </span>
                        </x-stats.mini-stat>
                    </x-cards.body>
                </x-cards.card>
            </div>
            <div class="col-12 col-sm-8">
                <x-cards.card>
                    <x-cards.body>
                        <x-stats.mini-stat :title="__('QSR Request')">
                                <span class="text-secondary">
                                    {{ $project->display_qsr_requested }}
                                </span>
                        </x-stats.mini-stat>
                    </x-cards.body>
                </x-cards.card>
            </div>
            <div class="col-24 col-sm-8">
                <x-cards.card>
                    <x-cards.body>
                        <x-stats.mini-stat :title="__('USD Value')" :stat="$project->display_usd_requested" />
                    </x-cards.body>
                </x-cards.card>
            </div>
            <div class="col-24">
                <x-cards.card>
                    <x-cards.body>
                        <x-accelerator-z.voting-info :item="$project" />
                    </x-cards.body>
                </x-cards.card>
            </div>
        </div>

        <x-cards.card class="mb-6 d-none">
            <x-cards.body>
                <div class="row gy-4">
                    <div class="col-12 col-sm-8">
                        <x-stats.mini-stat :title="__('ZNN Request')" :centered="true">
                            <span class="text-primary">
                                {{ $project->display_znn_requested }}
                            </span>
                        </x-stats.mini-stat>
                    </div>
                    <div class="col-12 col-sm-8">
                        <x-stats.mini-stat :title="__('QSR Request')" :centered="true">
                            <span class="text-secondary">
                                {{ $project->display_qsr_requested }}
                            </span>
                        </x-stats.mini-stat>
                    </div>
                    <div class="col-24 col-sm-8">
                        <x-stats.mini-stat :title="__('USD Request')" :centered="true">
                            <span class="text-white">
                                {{ $project->display_usd_requested }}
                            </span>
                        </x-stats.mini-stat>
                    </div>
                    <div class="col-24">
                        <hr>
                        <x-accelerator-z.voting-info :item="$project" />
                    </div>
                </div>
            </x-cards.body>
        </x-cards.card>

        <x-cards.card class="mb-6">
            <x-cards.body>
                <p>{{ $project->description }}</p>
                <hr>
                <div class="row">
                    <div class="col-24 col-lg-12">
                        <div class="vstack gap-2">
                            <x-stats.list-item :title="__('Link')">
                                <x-link :href="$project->url" :navigate="false" _target="_blank">
                                    {{ $project->url }}
                                </x-link>
                            </x-stats.list-item>
                            <x-stats.list-item :title="__('Owner')">
                                <x-address :account="$project->owner" :always-short="true" :copyable="true" />
                            </x-stats.list-item>
                            <x-stats.list-item :title="__('Total phases')" :hr="false">
                                {{ $project->phases->count() }}
                            </x-stats.list-item>
                            <hr class="d-block d-lg-none my-0 mb-3">
                        </div>
                    </div>
                    <div class="col-24 col-lg-12">
                        <div class="vstack gap-2">
                            <x-stats.list-item :title="__('ID')">
                                <x-hash :hash="$project->hash" :always-short="true" :copyable="true" />
                            </x-stats.list-item>
                            <x-stats.list-item :title="__('Created')">
                                <x-date-time.carbon :date="$project->created_at" />
                            </x-stats.list-item>
                            <x-stats.list-item :title="__('Updated')" :hr="false">
                                <x-date-time.carbon :date="$project->updated_at" />
                            </x-stats.list-item>
                        </div>
                    </div>
                </div>

                @if ($project->phases->count())
                    <hr>
                    <div class="list-group list-group-flush gap-4 mt-6">
                        @foreach ($project->phases as $phase)
                            <div class="list-group-item border rounded d-flex gap-3 p-4 bg-body-secondary-hover bg-body-tertiary card-hover">
                                <div class="w-100">
                                    <div class="d-flex align-items-center flex-fill">
                                        <x-link :href="route('accelerator-z.phase.detail', ['hash' => $phase->hash])" class="stretched-link text-heading">
                                            <div class="me-auto mb-0">
                                                <div class="text-muted text-xs">
                                                    Phase {{ $phase->phase_number }}
                                                </div>
                                                {{ $phase->name }}
                                            </div>
                                        </x-link>
                                        <div class="ms-auto">
                                            <span class="badge badge-md text-bg-{{ $phase->status->colour() }} bg-opacity-75">{{ $phase->status->label() }}</span>
                                        </div>
                                    </div>
                                    <hr>
                                    <x-accelerator-z.funding-info :item="$phase" />
                                    <x-accelerator-z.voting-info :item="$phase" />
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </x-cards.body>
        </x-cards.card>

    </div>

    <x-includes.header>
        <x-navigation.header.responsive-nav :items="[
            __('Votes') => route('accelerator-z.project.detail', ['hash' => $project->hash, 'tab' => 'votes']),
            __('JSON') => route('accelerator-z.project.detail', ['hash' => $project->hash, 'tab' => 'json'])
        ]" :active="$tab" />
    </x-includes.header>

    @if ($tab === 'votes')
        <livewire:accelerator-z.project-votes :project-id="$project->id" lazy />
    @endif

    @if ($tab === 'json')
        <div class="mx-3 mx-md-6">
            <x-code-highlighters.json :code="$project->raw_json" />
        </div>
    @endif
</x-app-layout>

