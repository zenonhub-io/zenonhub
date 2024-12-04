<x-app-layout>

    <x-includes.header :responsive-border="false">
        <div class="d-flex justify-content-between mb-4">
            <div class="d-flex align-items-start flex-column">
                <span class="text-muted text-xs">{{ __('Phase') }}</span>
                <div class="d-flex align-items-center">
                    <x-svg file="zenon/az" class="me-4" style="height: 28px "/>
                    <x-includes.header-title :title="$phase->name" />
                </div>
            </div>
            <div class="d-flex align-items-start">
                <span class="badge badge-md text-bg-{{ $phase->status->colour() }}">{{ $phase->status->label() }}</span>
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
                                    {{ $phase->display_znn_requested }}
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
                                    {{ $phase->display_qsr_requested }}
                                </span>
                        </x-stats.mini-stat>
                    </x-cards.body>
                </x-cards.card>
            </div>
            <div class="col-24 col-sm-8">
                <x-cards.card>
                    <x-cards.body>
                        <x-stats.mini-stat :title="__('USD Value')" :stat="$phase->display_usd_requested "/>
                    </x-cards.body>
                </x-cards.card>
            </div>
            <div class="col-24">
                <x-cards.card>
                    <x-cards.body>
                        <x-accelerator-z.voting-info :item="$phase" />
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
                                {{ $phase->display_znn_requested }}
                            </span>
                        </x-stats.mini-stat>
                    </div>
                    <div class="col-12 col-sm-8">
                        <x-stats.mini-stat :title="__('QSR Request')" :centered="true">
                            <span class="text-secondary">
                                {{ $phase->display_qsr_requested }}
                            </span>
                        </x-stats.mini-stat>
                    </div>
                    <div class="col-24 col-sm-8">
                        <x-stats.mini-stat :title="__('USD Request')" :centered="true">
                            <span class="text-white">
                                {{ $phase->display_usd_requested }}
                            </span>
                        </x-stats.mini-stat>
                    </div>
                    <div class="col-24">
                        <hr>
                        <x-accelerator-z.voting-info :item="$phase" />
                    </div>
                </div>
            </x-cards.body>
        </x-cards.card>

        <x-cards.card class="mb-6">
            <x-cards.body>
                <p>{{ $phase->description }}</p>
                <hr>
                <div class="row">
                    <div class="col-24 col-lg-12">
                        <div class="vstack gap-3">
                            <x-stats.list-item :title="__('Link')">
                                <x-link :href="$phase->url" :navigate="false" _target="_blank">
                                    {{ $phase->url }}
                                </x-link>
                            </x-stats.list-item>
                            <x-stats.list-item :title="__('Project')">
                                <x-link :href="route('accelerator-z.project.detail', ['hash' => $phase->project->hash])">
                                    {{ $phase->project->name }}
                                </x-link>
                            </x-stats.list-item>
                            <x-stats.list-item :title="__('Phase #')" :stat="$phase->phase_number" :hr="false" />
                            <hr class="d-block d-lg-none my-0 mb-3">
                        </div>
                    </div>
                    <div class="col-24 col-lg-12">
                        <div class="vstack gap-3">
                            <x-stats.list-item :title="__('ID')">
                                <x-hash :hash="$phase->hash" :always-short="true" :copyable="true" />
                            </x-stats.list-item>
                            <x-stats.list-item :title="__('Created')">
                                <x-date-time.carbon :date="$phase->created_at" />
                            </x-stats.list-item>
                            <x-stats.list-item :title="__('Updated')" :hr="false">
                                <x-date-time.carbon :date="$phase->updated_at" />
                            </x-stats.list-item>
                        </div>
                    </div>
                </div>
            </x-cards.body>
        </x-cards.card>

    </div>

    <x-includes.header>
        <x-navigation.header.responsive-nav :items="[
            __('Votes') => route('accelerator-z.phase.detail', ['hash' => $phase->hash, 'tab' => 'votes']),
            __('JSON') => route('accelerator-z.phase.detail', ['hash' => $phase->hash, 'tab' => 'json'])
        ]" :active="$tab" />
    </x-includes.header>

    @if ($tab === 'votes')
        <livewire:accelerator-z.phase-votes :phase-id="$phase->id" lazy />
    @endif

    @if ($tab === 'json')
        <div class="mx-3 mx-md-6">
            <x-code-highlighters.json :code="$phase->raw_json" />
        </div>
    @endif
</x-app-layout>

