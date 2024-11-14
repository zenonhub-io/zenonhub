<x-app-layout>

    <x-includes.header :title="__('Accelerator-Z')" class="mb-4">
        <x-navigation.header.responsive-nav :items="[
            __('Overview') => route('stats.accelerator-z'),
            __('Engagement') => route('stats.accelerator-z', ['tab' => 'engagement']),
            __('Contributors') => route('stats.accelerator-z', ['tab' => 'contributors']),
        ]" :active="$tab" />
    </x-includes.header>

    @if ($tab === 'overview')
        <div class="container-fluid px-3 px-md-6">
            <div class="row mb-6 gy-6">
                <div class="col-12 col-sm-8">
                    <x-cards.card>
                        <x-cards.body>
                            <x-stats.mini-stat :title="__('ZNN Available')">
                                <span class="text-primary">
                                    {{ $data['znnBalance'] }}
                                </span>
                            </x-stats.mini-stat>
                        </x-cards.body>
                    </x-cards.card>
                </div>
                <div class="col-12 col-sm-8">
                    <x-cards.card>
                        <x-cards.body>
                            <x-stats.mini-stat :title="__('QSR Available')">
                                <span class="text-secondary">
                                    {{ $data['qsrBalance'] }}
                                </span>
                            </x-stats.mini-stat>
                        </x-cards.body>
                    </x-cards.card>
                </div>
                <div class="col-24 col-sm-8">
                    <x-cards.card>
                        <x-cards.body>
                            <x-stats.mini-stat :title="__('USD Value')" :stat="$data['usdBalance']"/>
                        </x-cards.body>
                    </x-cards.card>
                </div>
            </div>
            <div class="row mb-6 gy-6 d-none">
                <div class="col-12 col-lg-8">
                    <x-cards.card>
                        <x-cards.body>
                            <x-stats.mini-stat :title="__('ZNN Paid')">
                                <span class="text-primary">
                                    {{ $data['znnPaid'] }}
                                </span>
                            </x-stats.mini-stat>
                        </x-cards.body>
                    </x-cards.card>
                </div>
                <div class="col-12 col-lg-8">
                    <x-cards.card>
                        <x-cards.body>
                            <x-stats.mini-stat :title="__('QSR Paid')">
                                <span class="text-secondary">
                                    {{ $data['qsrPaid'] }}
                                </span>
                            </x-stats.mini-stat>
                        </x-cards.body>
                    </x-cards.card>
                </div>
                <div class="col-24 col-lg-8">
                    <x-cards.card>
                        <x-cards.body>
                            <x-stats.mini-stat :title="__('Complete Projects')" :stat="$data['completeProjects']"/>
                        </x-cards.body>
                    </x-cards.card>
                </div>
            </div>
            <div class="row mb-6 gy-6">
                <div class="col-24 col-md-12">
                    <x-cards.card>
                        <x-cards.heading :title="__('ZNN Paid/Remaining')" />
                        <x-cards.body class="p-2" style="height: 12rem">
                            <livewire:livewire-pie-chart
                                key="{{ $data['znnDonutChart']->reactiveKey() }}"
                                :pie-chart-model="$data['znnDonutChart']"
                            />
                        </x-cards.body>
                    </x-cards.card>
                </div>
                <div class="col-24 col-md-12">
                    <x-cards.card>
                        <x-cards.heading :title="__('QSR Paid/Remaining')" />
                        <x-cards.body class="p-2" style="height: 12rem">
                            <livewire:livewire-pie-chart
                                key="{{ $data['qsrDonutChart']->reactiveKey() }}"
                                :pie-chart-model="$data['qsrDonutChart']"
                            />
                        </x-cards.body>
                    </x-cards.card>
                </div>
            </div>
        </div>
    @endif

    @if ($tab === 'engagement')
        <livewire:stats.az.engagement-list />
    @endif

    @if ($tab === 'contributors')
        <livewire:stats.az.contributors-list />
    @endif

</x-app-layout>
