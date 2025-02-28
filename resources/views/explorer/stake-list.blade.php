<x-app-layout>

    <x-includes.header :title="__('Staking')">
        @if (app('znnEthLpToken'))
            <x-navigation.header.responsive-nav :items="[
                app('znnToken')->symbol => route('explorer.stake.list'),
                app('znnEthLpToken')->symbol => route('explorer.stake.list', ['tab' => 'znn-eth-lp']),
            ]" :active="$tab" />
        @endif
    </x-includes.header>

    <div class="container-fluid px-3 px-md-6">
        <div class="row mb-6 gy-6">
            <div class="col-12 col-lg-6">
                <x-cards.card>
                    <x-cards.body>
                        <x-stats.mini-stat
                            :title="__('Total Staked')"
                            :stat="$stats['stakedTotal']"
                        />
                    </x-cards.body>
                </x-cards.card>
            </div>
            <div class="col-12 col-lg-6">
                <x-cards.card>
                    <x-cards.body>
                        <x-stats.mini-stat
                            :title="__('Avg. Duration')"
                            :stat="$stats['avgDuration'] .' ' . __('Days')"
                        />
                    </x-cards.body>
                </x-cards.card>
            </div>
            <div class="col-12 col-lg-6">
                <x-cards.card>
                    <x-cards.body>
                        <x-stats.mini-stat
                            :title="__('Total Stakers')"
                            :stat="$stats['stakersCount']"
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
        </div>
    </div>
    <livewire:explorer.staking-list lazy :tab="$tab" />
</x-app-layout>
