<x-app-layout>
    <x-includes.header :responsive-border="false">
        <div class="d-flex justify-content-between mb-4">
            <div class="d-flex align-items-start flex-column">
                <div class="d-flex align-items-center mb-1">
                    <div class="title-icon">
                        <x-svg file="zenon/az" />
                    </div>
                    <h5 class="text-muted ms-3">{{ __('Phase') }}</h5>
                </div>
                <x-includes.header-title>
                    <h1 class="ls-tight text-wrap text-break">
                        {{ $phase->name }}
                        <x-copy :text="route('accelerator-z.phase.detail', ['hash' => $phase->hash])" class="ms-2 text-md" :tooltip="__('Copy URL')" />
                    </h1>
                </x-includes.header-title>
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
                        <x-stats.mini-stat :title="__('USD Value')" :stat="$phase->display_usd_requested" />
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
                        <div class="vstack gap-2">
                            <x-stats.list-item :title="__('Link')">
                                <x-link :href="external_url($phase->url)" :navigate="false" :new-tab="true">
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
                        <div class="vstack gap-2">
                            <x-stats.list-item :title="__('ID')">
                                <x-hash :hash="$phase->hash" :always-short="true" :copyable="true" />
                            </x-stats.list-item>
                            <x-stats.list-item :title="__('Created')">
                                <x-date-time.carbon :date="$phase->created_at" />
                            </x-stats.list-item>
                            <x-stats.list-item :title="__('Updated')" :hr="false">
                                @if ($phase->updated_at)
                                    <x-date-time.carbon :date="$phase->updated_at" />
                                @else
                                    -
                                @endif
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
            <x-cards.card>
                <x-cards.body>
                    <x-code-highlighters.json :code="$phase->raw_json" />
                </x-cards.body>
            </x-cards.card>
        </div>
    @endif
</x-app-layout>

