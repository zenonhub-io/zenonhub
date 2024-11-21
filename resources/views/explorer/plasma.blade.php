<x-app-layout>
    <x-includes.header :title="__('Plasma')" class="mb-4" />
    <div class="container-fluid px-3 px-md-6">
        <div class="row mb-6 gy-6">
            <div class="col-12 col-lg-8">
                <x-cards.card>
                    <x-cards.body>
                        <x-stats.mini-stat
                            :title="__('Total Fused')"
                            :stat="$stats['plasma_total'] . ' QSR'"
                        />
                    </x-cards.body>
                </x-cards.card>
            </div>
            <div class="col-12 col-lg-8">
                <x-cards.card>
                    <x-cards.body>
                        <x-stats.mini-stat
                            :title="__('Total Fusions')"
                            :stat="$stats['fusions_count']"
                        />
                    </x-cards.body>
                </x-cards.card>
            </div>
            <div class="col-12 col-lg-8">
                <x-cards.card>
                    <x-cards.body>
                        <x-stats.mini-stat
                            :title="__('Total Receivers')"
                            :stat="$stats['account_count']"
                        />
                    </x-cards.body>
                </x-cards.card>
            </div>
        </div>
    </div>
    <livewire:explorer.plasma-list />
</x-app-layout>
