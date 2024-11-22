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
                                    {{ $stats['znnBalance'] }}
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
                                    {{ $stats['qsrBalance'] }}
                                </span>
                            </x-stats.mini-stat>
                        </x-cards.body>
                    </x-cards.card>
                </div>
                <div class="col-24 col-sm-8">
                    <x-cards.card>
                        <x-cards.body>
                            <x-stats.mini-stat :title="__('USD Value')" :stat="$stats['usdBalance']"/>
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
                                    {{ $stats['znnPaid'] }}
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
                                    {{ $stats['qsrPaid'] }}
                                </span>
                            </x-stats.mini-stat>
                        </x-cards.body>
                    </x-cards.card>
                </div>
                <div class="col-24 col-lg-8">
                    <x-cards.card>
                        <x-cards.body>
                            <x-stats.mini-stat :title="__('Complete Projects')" :stat="$stats['completeProjects']"/>
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
                                key="{{ $stats['znnDonutChart']->reactiveKey() }}"
                                :pie-chart-model="$stats['znnDonutChart']"
                            />
                        </x-cards.body>
                    </x-cards.card>
                </div>
                <div class="col-24 col-md-12">
                    <x-cards.card>
                        <x-cards.heading :title="__('QSR Paid/Remaining')" />
                        <x-cards.body class="p-2" style="height: 12rem">
                            <livewire:livewire-pie-chart
                                key="{{ $stats['qsrDonutChart']->reactiveKey() }}"
                                :pie-chart-model="$stats['qsrDonutChart']"
                            />
                        </x-cards.body>
                    </x-cards.card>
                </div>
            </div>
        </div>

    @endif

    @if ($tab === 'engagement')

        <div class="container-fluid px-3 px-md-6">
            <div class="row mb-6 gy-6">
                <div class="col-12 col-lg-6">
                    <x-cards.card>
                        <x-cards.body>
                            <x-stats.mini-stat :title="__('Voting Pillars')">
                                {{ $stats['votingPillars'] }}
                            </x-stats.mini-stat>
                        </x-cards.body>
                    </x-cards.card>
                </div>
                <div class="col-12 col-lg-6">
                    <x-cards.card>
                        <x-cards.body>
                            <x-stats.mini-stat :title="__('Engaged Pillars')">
                                {{ $stats['percentageVotingPillars'] }}%
                            </x-stats.mini-stat>
                        </x-cards.body>
                    </x-cards.card>
                </div>
                <div class="col-12 col-lg-6">
                    <x-cards.card>
                        <x-cards.body>
                            <x-stats.mini-stat :title="__('Avg Voting Time')">
                                {{ $stats['avgVoteTime'] }}
                            </x-stats.mini-stat>
                        </x-cards.body>
                    </x-cards.card>
                </div>
                <div class="col-12 col-lg-6">
                    <x-cards.card>
                        <x-cards.body>
                            <x-stats.mini-stat :title="__('Accepted Projects')">
                                {{ $stats['percentageAcceptedProjects'] }}%
                            </x-stats.mini-stat>
                        </x-cards.body>
                    </x-cards.card>
                </div>
            </div>
        </div>

        <livewire:stats.az.engagement-list />
    @endif

    @if ($tab === 'contributors')

        <div class="container-fluid px-3 px-md-6">
            <div class="row mb-6 gy-6">
                <div class="col-12 col-lg-6">
                    <x-cards.card>
                        <x-cards.body>
                            <x-stats.mini-stat :title="__('Total Contributors')">
                                {{ $stats['totalContributors'] }}
                            </x-stats.mini-stat>
                        </x-cards.body>
                    </x-cards.card>
                </div>
                <div class="col-12 col-lg-6">
                    <x-cards.card>
                        <x-cards.body>
                            <x-stats.mini-stat :title="__('Complete Projects')">
                                {{ $stats['completeProjects'] }}
                            </x-stats.mini-stat>
                        </x-cards.body>
                    </x-cards.card>
                </div>
                <div class="col-12 col-lg-6">
                    <x-cards.card>
                        <x-cards.body>
                            <x-stats.mini-stat :title="__('ZNN Paid')">
                                <span class="text-primary">
                                    {{ $stats['znnPaid'] }}
                                </span>
                            </x-stats.mini-stat>
                        </x-cards.body>
                    </x-cards.card>
                </div>
                <div class="col-12 col-lg-6">
                    <x-cards.card>
                        <x-cards.body>
                            <x-stats.mini-stat :title="__('QSR Paid')">
                                <span class="text-secondary">
                                    {{ $stats['qsrPaid'] }}
                                </span>
                            </x-stats.mini-stat>
                        </x-cards.body>
                    </x-cards.card>
                </div>
            </div>
        </div>

        <livewire:stats.az.contributors-list />
    @endif

</x-app-layout>
