<x-app-layout>

    <x-includes.header :responsive-border="false">
        <div class="d-flex justify-content-between mb-4">
            <div class="d-flex align-items-start flex-column">
                <div class="d-flex align-items-center">
                    <x-svg file="zenon/az" class="me-4" style="height: 28px"/>
                    <x-includes.header-title :title="$project->name" />
                </div>
            </div>
            <div class="d-flex align-items-start">
                <span class="badge text-bg-{{ $project->status->colour() }}">{{ $project->status->label() }}</span>
            </div>
        </div>
    </x-includes.header>

    <div class="container-fluid px-3 px-md-6">
        <x-cards.card class="mb-6">
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
                    <div class="col-12 col-sm-8 d-none d-sm-block">
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
                        <div class="vstack gap-3">
                            <x-stats.list-item :title="__('Link')">
                                <x-link :href="$project->url" :navigate="false" _target="_blank">
                                    {{ $project->url }}
                                </x-link>
                            </x-stats.list-item>
                            <x-stats.list-item :title="__('Owner')">
                                <x-address :account="$project->owner" :named="false"/>
                            </x-stats.list-item>
                            <x-stats.list-item :title="__('Created')" :hr="false">
                                <x-date-time.carbon :date="$project->created_at" />
                            </x-stats.list-item>
                            <hr class="d-block d-md-none my-0 mb-3">
                        </div>
                    </div>
                    <div class="col-24 col-lg-12">
                        <div class="vstack gap-3">
                            <x-stats.list-item :title="__('Total phases')">
                                {{ $project->phases->count() }}
                            </x-stats.list-item>
                            <x-stats.list-item :title="__('ID')">
                                <x-hash :hash="$project->hash" :always-short="true" />
                            </x-stats.list-item>
                            <x-stats.list-item :title="__('Updated')" :hr="false">
                                <x-date-time.carbon :date="$project->updated_at" />
                            </x-stats.list-item>
                        </div>
                    </div>
                </div>
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
        <livewire:accelerator-z.project-votes :project-id="$project->id" />
    @endif

    @if ($tab === 'json')
        <x-code-highlighters.json :code="$project->raw_json" />
    @endif
</x-app-layout>

