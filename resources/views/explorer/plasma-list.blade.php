<x-app-layout>
    <x-includes.header :title="__('Plasma')" class="mb-4" />
    <div class="container-fluid px-3 px-md-6">
        <div class="row mb-6 gy-6">
            <div class="col-12 col-lg-8">
                <x-cards.card>
                    <x-cards.body>
                        <x-stats.mini-stat
                            :title="__('Total Fused')"
                            :stat="$stats['plasmaTotal'] . ' QSR'"
                        />
                    </x-cards.body>
                </x-cards.card>
            </div>
            <div class="col-12 col-lg-8">
                <x-cards.card>
                    <x-cards.body>
                        <x-stats.mini-stat
                            :title="__('Total Fusions')"
                            :stat="$stats['fusionsCount']"
                        />
                    </x-cards.body>
                </x-cards.card>
            </div>
            <div class="col-12 col-lg-8">
                <x-cards.card>
                    <x-cards.body>
                        <x-stats.mini-stat
                            :title="__('Total Receivers')"
                            :stat="$stats['accountCount']"
                        />
                    </x-cards.body>
                </x-cards.card>
            </div>
        </div>
    </div>
    <livewire:explorer.plasma-list lazy />
</x-app-layout>
