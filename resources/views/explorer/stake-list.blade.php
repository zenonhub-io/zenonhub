<x-app-layout>
    <x-includes.header :title="__('Staking')" class="mb-4" />
    <div class="container-fluid px-3 px-md-6">
        <div class="row mb-6 gy-6">
            <div class="col-12 col-lg-8">
                <x-cards.card>
                    <x-cards.body>
                        <x-stats.mini-stat
                            :title="__('Total Staked')"
                            :stat="$stats['stakedTotal'] . ' ZNN'"
                        />
                    </x-cards.body>
                </x-cards.card>
            </div>
            <div class="col-12 col-lg-8">
                <x-cards.card>
                    <x-cards.body>
                        <x-stats.mini-stat
                            :title="__('Total Stakes')"
                            :stat="$stats['stakesCount']"
                        />
                    </x-cards.body>
                </x-cards.card>
            </div>
            <div class="col-12 col-lg-8">
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
    <livewire:explorer.staking-list />
</x-app-layout>
