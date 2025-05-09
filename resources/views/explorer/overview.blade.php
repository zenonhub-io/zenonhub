<x-app-layout>
    <x-includes.header :title="__('Explorer Overview')" :responsive-border="false" />
    <div class="container-fluid px-3 px-md-6">
        <div class="row mb-6 gy-6">
            <div class="col-12 col-lg-6">
                <x-cards.card class="bg-body-secondary-hover card-hover">
                    <x-link :href="route('explorer.momentum.list')" class="stretched-link">
                        <x-cards.body>
                            <x-stats.mini-stat
                                :title="__('Momentums')"
                                :stat="$stats['momentums']"
                            />
                        </x-cards.body>
                    </x-link>
                </x-cards.card>
            </div>
            <div class="col-12 col-lg-6">
                <x-cards.card class="bg-body-secondary-hover card-hover">
                    <x-link :href="route('explorer.transaction.list')" class="stretched-link">
                        <x-cards.body>
                            <x-stats.mini-stat
                                :title="__('Transactions')"
                                :stat="$stats['transactions']"
                            />
                        </x-cards.body>
                    </x-link>
                </x-cards.card>
            </div>
            <div class="col-12 col-lg-6">
                <x-cards.card class="bg-body-secondary-hover card-hover">
                    <x-link :href="route('explorer.account.list')" class="stretched-link">
                        <x-cards.body>
                            <x-stats.mini-stat
                                :title="__('Addresses')"
                                :stat="$stats['addresses']"
                            />
                        </x-cards.body>
                    </x-link>
                </x-cards.card>
            </div>
            <div class="col-12 col-lg-6">
                <x-cards.card class="bg-body-secondary-hover card-hover">
                    <x-link :href="route('explorer.token.list')" class="stretched-link">
                        <x-cards.body>
                            <x-stats.mini-stat
                                :title="__('Tokens')"
                                :stat="$stats['tokens']"
                            />
                        </x-cards.body>
                    </x-link>
                </x-cards.card>
            </div>
        </div>
        <div class="row mb-6 gy-6">
            <div class="col-24 col-lg-12">
                <livewire:explorer.overview.transactions-daily lazy />
            </div>
            <div class="col-24 col-lg-12">
                <livewire:explorer.overview.accounts-active lazy />
            </div>
        </div>
        <div class="row mb-6 gy-6">
            <div class="col-24 col-lg-12">
                <livewire:explorer.overview.momentums-latest lazy />
            </div>
            <div class="col-24 col-lg-12">
                <livewire:explorer.overview.transactions-latest lazy />
            </div>
        </div>
    </div>
</x-app-layout>

