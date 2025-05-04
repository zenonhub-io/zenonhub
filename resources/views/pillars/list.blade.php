<x-app-layout>

    <x-slot:breadcrumbs>
        {{ Breadcrumbs::render('pillar.list') }}
    </x-slot>

    <x-includes.header :title="__('Pillars')" class="mb-4" />
    <div class="container-fluid px-3 px-md-6">
        <div class="row mb-6 gy-6">
            <div class="col-12 col-lg-6">
                <x-cards.card>
                    <x-cards.body>
                        <x-stats.mini-stat
                            :title="__('Active')"
                            :stat="$stats['active']"
                        />
                    </x-cards.body>
                </x-cards.card>
            </div>
            <div class="col-12 col-lg-6">
                <x-cards.card>
                    <x-cards.body>
                        <x-stats.mini-stat
                            :title="__('Inactive')"
                            :stat="$stats['inactive']"
                        />
                    </x-cards.body>
                </x-cards.card>
            </div>
            <div class="col-12 col-lg-6">
                <x-cards.card>
                    <x-cards.body>
                        <x-stats.mini-stat
                            :title="__('Avg. APR')"
                            :stat="$stats['avgApr'] . '%'"
                        />
                    </x-cards.body>
                </x-cards.card>
            </div>
            <div class="col-12 col-lg-6">
                <x-cards.card>
                    <x-cards.body>
                        <x-stats.mini-stat
                            :title="__('Delegated ZNN')"
                            :stat="$stats['delegatedZnn']"
                        />
                    </x-cards.body>
                </x-cards.card>
            </div>
        </div>
    </div>

    <x-includes.header class="mb-4">
        <x-navigation.header.responsive-nav :items="[
            __('All') => route('pillar.list'),
            __('Active') => route('pillar.list', ['tab' => 'active']),
            __('Inactive') => route('pillar.list', ['tab' => 'inactive']),
            __('Revoked') => route('pillar.list', ['tab' => 'revoked'])
        ]" :active="$tab" />
    </x-includes.header>

    <livewire:pillars.pillar-list :tab="$tab" lazy />

</x-app-layout>

