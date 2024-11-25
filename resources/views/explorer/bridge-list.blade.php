<x-app-layout>
    <x-includes.header :title="__('Bridge')">
        <x-navigation.header.responsive-nav :items="[
            __('Inbound') => route('explorer.bridge.list'),
            __('Outbound') => route('explorer.bridge.list', ['tab' => 'outbound']),
            __('ETH LP') => route('explorer.bridge.list', ['tab' => 'znn-eth-lp']),
        ]" :active="$tab" />
    </x-includes.header>

    @if ($tab === 'inbound')
        <livewire:explorer.bridge.inbound-list />
    @endif

    @if ($tab === 'outbound')
        <livewire:explorer.bridge.outbound-list />
    @endif

    @if ($tab === 'znn-eth-lp')

        <div class="container-fluid px-3 px-md-6">
            <div class="row mb-6 gy-6">
                <div class="col-24 col-lg-12">
                    <x-cards.card>
                        <x-cards.body>
                            <x-stats.mini-stat
                                :title="__('Total Staked')"
                                :stat="$stats['stakedTotal'] . ' ZNN ETH LP'"
                            />
                        </x-cards.body>
                    </x-cards.card>
                </div>
                <div class="col-12 col-lg-6">
                    <x-cards.card>
                        <x-cards.body>
                            <x-stats.mini-stat
                                :title="__('Total Stakes')"
                                :stat="$stats['stakesCount']"
                            />
                        </x-cards.body>
                    </x-cards.card>
                </div>
                <div class="col-12 col-lg-6">
                    <x-cards.card>
                        <x-cards.body>
                            <x-stats.mini-stat
                                :title="__('Avg Duration')"
                                :stat="$stats['avgDuration'] .' ' . __('Days')"
                            />
                        </x-cards.body>
                    </x-cards.card>
                </div>
            </div>
        </div>

        <livewire:explorer.bridge.znn-eth-lp-staking-list />
    @endif

</x-app-layout>
