<x-app-layout>
{{--    <x-includes.header :title="__('Overview')" class="mb-4" />--}}

    <div class="container-fluid px-3 px-md-6">
        <div class="row mb-6 gy-6">
            <div class="col-12 col-lg-6">
                <x-cards.card>
                    <x-cards.body>
                        <x-stats.mini-stat
                            :title="__('Momentums')"
                            :stat="$stats['momentums']"
                        />
                    </x-cards.body>
                </x-cards.card>
            </div>
            <div class="col-12 col-lg-6">
                <x-cards.card>
                    <x-cards.body>
                        <x-stats.mini-stat
                            :title="__('Transactions')"
                            :stat="$stats['transactions']"
                        />
                    </x-cards.body>
                </x-cards.card>
            </div>
            <div class="col-12 col-lg-6">
                <x-cards.card>
                    <x-cards.body>
                        <x-stats.mini-stat
                            :title="__('Addresses')"
                            :stat="$stats['addresses']"
                        />
                    </x-cards.body>
                </x-cards.card>
            </div>
            <div class="col-12 col-lg-6">
                <x-cards.card>
                    <x-cards.body>
                        <x-stats.mini-stat
                            :title="__('Tokens')"
                            :stat="$stats['tokens']"
                        />
                    </x-cards.body>
                </x-cards.card>
            </div>
        </div>

        <div class="row mb-6 gy-6">
            <div class="col-24 col-lg-12">
                <livewire:explorer.transactions-overview />
            </div>
            <div class="col-24 col-md-12"></div>
        </div>
    </div>

</x-app-layout>

