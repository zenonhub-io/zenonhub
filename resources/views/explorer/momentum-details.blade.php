<x-app-layout>
    <x-includes.header :responsive-border="false">
        <div class="d-flex justify-content-between mb-4">
            <div class="d-flex align-items-start flex-column">
                <span class="text-muted text-xs">{{ __('Momentum') }}</span>
                <div class="d-flex align-items-center mb-1">
                    <x-includes.header-title>
                        <h1 class="ls-tight text-wrap text-break">
                            <x-hash :hash="$momentum->hash" :always-short="true" :either-side="6" />
                        </h1>
                    </x-includes.header-title>
                </div>
            </div>
        </div>
    </x-includes.header>

    <div class="container-fluid px-3 px-md-6">
        <div class="row mb-6 gy-6">
            <div class="col-12">
                <x-cards.card>
                    <x-cards.body>
                        <x-stats.mini-stat
                            :title="__('Height')"
                            :info="__('Current height of the Network of Momentum')"
                            :stat="$momentum->display_height"
                        />
                    </x-cards.body>
                </x-cards.card>
            </div>
            <div class="col-12">
                <x-cards.card>
                    <x-cards.body>
                        <x-stats.mini-stat
                            :title="__('Producer')"
                            :info="__('The pillar that produced the momentum')">
                                <x-link :href="route('pillar.detail', ['slug' => $momentum->producerPillar->slug])">
                                    {{ $momentum->producerPillar->slug }}
                                </x-link>
                        </x-stats.mini-stat>
                    </x-cards.body>
                </x-cards.card>
            </div>
        </div>
        <x-cards.card class="mb-6">
            <x-cards.body>
                <div class="row">
                    <div class="col-24 col-lg-12">
                        <div class="vstack gap-2">
                            <x-stats.list-item :title="__('Hash')">
                                <x-hash :hash="$momentum->hash" :always-short="true" :copyable="true" />
                            </x-stats.list-item>
                            <x-stats.list-item :title="__('Created')" :hr="false">
                                <x-date-time.carbon :date="$momentum->created_at" />
                            </x-stats.list-item>
                            <hr class="d-block d-md-none my-0 mb-3">
                        </div>
                    </div>
                    <div class="col-24 col-lg-12">
                        <div class="vstack gap-2">
                            <x-stats.list-item :title="__('Producer')">
                                <x-address :account="$momentum->producerAccount" :always-short="true" :copyable="true" />
                            </x-stats.list-item>
                            <x-stats.list-item :title="__('Transactions')" :stat="$momentum->account_blocks_count" :hr="false" />
                        </div>
                    </div>
                </div>
            </x-cards.body>
        </x-cards.card>
    </div>

    <x-includes.header>
        <x-navigation.header.responsive-nav :items="[
            __('Transactions') => route('explorer.momentum.detail', ['hash' => $momentum->hash, 'tab' => 'transactions']),
            __('JSON') => route('explorer.momentum.detail', ['hash' => $momentum->hash, 'tab' => 'json']),
        ]" :active="$tab" />
    </x-includes.header>

    @if ($tab === 'transactions')
        <livewire:explorer.momentum.transactions-list :momentumId="$momentum->id" lazy />
    @endif

    @if ($tab === 'json')
        <div class="mx-3 mx-md-6">
            <x-code-highlighters.json :code="$momentum->raw_json" />
        </div>
    @endif
</x-app-layout>
