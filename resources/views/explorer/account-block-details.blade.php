<x-app-layout>
    <x-includes.header :responsive-border="false">
        <div class="row justify-content-between align-items-center mb-4">
            <div class="col-24 col-sm">
                <div class="d-flex align-items-start flex-column">
                    <h5 class="text-muted">
                        {{ __('Account Block') }}
                        <x-copy :text="$block->hash" class="ms-2" :tooltip="__('Copy hash')"/>
                    </h5>
                    <div class="d-flex align-items-center">
                        <x-includes.header-title>
                            <h1 class="ls-tight text-wrap text-break">
                                {{ short_hash($block->hash, 6) }}
                            </h1>
                        </x-includes.header-title>
                    </div>
                </div>
            </div>
            <div class="col-24 col-sm-auto">
                <div
                    class="d-flex justify-content-between gap-1 p-1 align-items-center bg-dark-subtle rounded border text-xs fw-semibold mt-1 mt-sm-0 shadow-inset">
                    @if($block->previous_block)
                        <x-link
                            :href="route('explorer.block.detail', ['hash' => $block->previous_block->hash])"
                            class="px-3 py-1 text-muted bg-dark-hover bg-opacity-70-hover rounded"
                            data-bs-toggle="tooltip"
                            data-bs-title="{{ __('Previous') }}"
                        >
                            <i class="bi bi-chevron-left"></i> <span
                                class="d-inline d-sm-none">{{ __('Previous') }}</span>
                        </x-link>
                    @else
                        <span class="px-3 py-1 text-muted bg-dark-subtle rounded">
                            <i class="bi bi-chevron-left"></i> <span
                                class="d-inline d-sm-none">{{ __('Previous') }}</span>
                        </span>
                    @endif
                    @if($block->next_block)
                        <x-link
                            :href="route('explorer.block.detail', ['hash' => $block->next_block->hash])"
                            class="px-3 py-1 text-muted bg-dark-hover bg-opacity-70-hover rounded"
                            data-bs-toggle="tooltip"
                            data-bs-title="{{ __('Next') }}"
                        >
                            <span class="d-inline d-sm-none">{{ __('Next') }}</span> <i class="bi bi-chevron-right"></i>
                        </x-link>
                    @else
                        <span class="px-3 py-1 text-muted bg-dark-subtle rounded">
                            <span class="d-inline d-sm-none">{{ __('Next') }}</span> <i class="bi bi-chevron-right"></i>
                        </span>
                    @endif
                </div>
            </div>
        </div>
    </x-includes.header>

    <div class="container-fluid px-3 px-md-6">

        <x-cards.card class="mb-6 gy-6">
            <x-cards.body>
                <div class="d-md-flex align-items-center justify-content-start justify-content-md-between">
                    <div class="col-24 col-md-10 text-center">
                        <span class="text-muted text-sm d-block">{{ __('From') }}</span>
                        <x-address :account="$block->account" :always-short="true" :copyable="true"/>
                    </div>
                    <div class="col-24 col-md-4 text-center py-4 py-md-0">
                        @if($block->is_received)
                            <span class="d-none d-md-block">
                                <x-svg file="explorer/send" class="text-success" style="transform: rotate(90deg);"/>
                            </span>
                            <span class="d-block d-md-none">
                                <x-svg file="explorer/send" class="text-success" style="transform: rotate(180deg);"/>
                            </span>
                        @else
                            <span data-bs-toggle="tooltip" data-bs-title="{{ __('Unreceived') }}">
                                <x-svg file="explorer/unreceived" class="text-danger"/>
                            </span>
                        @endif
                    </div>
                    <div class="col-24 col-md-10 text-center">
                        <span class="text-muted text-sm d-block">{{ __('To') }}</span>
                        <x-address :account="$block->toAccount" :always-short="true" :copyable="true"/>
                    </div>
                </div>
            </x-cards.body>
        </x-cards.card>

        <x-cards.card class="mb-6">
            <x-cards.body>
                <div class="row">
                    <div class="col-24 col-lg-12">
                        <div class="vstack gap-2">
                            <x-stats.list-item :title="__('Hash')">
                                <x-hash :hash="$block->hash" :always-short="true" :copyable="true"/>
                            </x-stats.list-item>
                            <x-stats.list-item :title="__('Type')" :stat="$block->display_actual_type"/>
                            <x-stats.list-item :title="__('Amount')">
                                @if ($block->token && $block->amount > 0)
                                    {{ $block->display_amount }}
                                    <x-link
                                        :href="route('explorer.token.detail', ['zts' => $block->token->token_standard])">
                                        {{ $block->token->symbol }}
                                    </x-link>
                                @else
                                    -
                                @endif
                            </x-stats.list-item>
                            <x-stats.list-item :title="__('Timestamp')">
                                <x-date-time.carbon :date="$block->created_at"/>
                            </x-stats.list-item>
                            <x-stats.list-item :title="__('Received')" :hr="false">
                                @if($block->pairedAccountBlock)
                                    <x-date-time.carbon :date="$block->pairedAccountBlock->created_at"/>
                                @else
                                    -
                                @endif
                            </x-stats.list-item>
                            <hr class="d-block d-lg-none my-0 mb-3">
                        </div>
                    </div>
                    <div class="col-24 col-lg-12">
                        <div class="vstack gap-2">
                            <x-stats.list-item :title="__('Confirmations')">
                                @if ($block->raw_json?->confirmationDetail?->numConfirmations)
                                    {{ number_format($block->raw_json->confirmationDetail->numConfirmations) }}
                                @else
                                    -
                                @endif
                            </x-stats.list-item>
                            <x-stats.list-item :title="__('Momentum')">
                                <x-hash :hash="$block->momentum->hash" :always-short="true" :copyable="true"
                                        :link="route('explorer.momentum.detail', ['hash' => $block->momentum->hash])"/>
                            </x-stats.list-item>
                            <x-stats.list-item :title="__('Paired Block')">
                                @if($block->pairedAccountBlock)
                                    <x-hash :hash="$block->pairedAccountBlock->hash" :always-short="true"
                                            :copyable="true"
                                            :link="route('explorer.block.detail', ['hash' => $block->pairedAccountBlock->hash])"/>
                                @else
                                    -
                                @endif
                            </x-stats.list-item>
                            <x-stats.list-item :title="__('Parent Block')">
                                @if($block->parent)
                                    <x-hash :hash="$block->parent->hash" :always-short="true" :copyable="true"
                                            :link="route('explorer.block.detail', ['hash' => $block->parent->hash])"/>
                                @else
                                    -
                                @endif
                            </x-stats.list-item>
                            <x-stats.list-item :title="__('Descendants')" :hr="false">
                                {{ $block->descendants_count }}
                            </x-stats.list-item>
                        </div>
                    </div>
                </div>
            </x-cards.body>
        </x-cards.card>
    </div>

    <x-includes.header>
        <x-navigation.header.responsive-nav :items="[
            __('Data') => route('explorer.block.detail', ['hash' => $block->hash, 'tab' => 'data']),
            __('Descendants') => route('explorer.block.detail', ['hash' => $block->hash, 'tab' => 'descendants']),
            __('JSON') => route('explorer.block.detail', ['hash' => $block->hash, 'tab' => 'json']),
        ]" :active="$tab"/>
    </x-includes.header>

    @if ($tab === 'data')
        <div class="mx-3 mx-md-6 mb-4">
            @if($block->data)
                <x-cards.card>
                    <x-cards.body>
                        <h4 class="mb-3">{{ __('Decoded') }}</h4>
                        <pre class="line-numbers mb-0 p-4 border rounded bg-body-tertiary shadow-inset"><code class="lang-json">{{ json_encode($block->data->decoded, JSON_PRETTY_PRINT) }}</code></pre>
                        <hr class="my-6">
                        <h4 class="mb-3">{{ __('Raw') }}</h4>
                        <pre class="line-numbers mb-0 p-4 border rounded bg-body-tertiary shadow-inset text-wrap">{{ $block->data->raw }}</pre>
                    </x-cards.body>
                </x-cards.card>
            @else
                <x-alerts.alert type="info">
                    <i class="bi bi-info-circle-fill me-2"></i> {{ __('No block data') }}
                </x-alerts.alert>
            @endif
        </div>
    @endif

    @if ($tab === 'descendants')
        <livewire:explorer.account-block.descendants-list :blockId="$block->id" lazy/>
    @endif

    @if ($tab === 'json')
        <div class="mx-3 mx-md-6">
            <x-cards.card>
                <x-cards.body>
                    <x-code-highlighters.json
                        :code="$block->raw_json ?: ['error' => __('Data unavailable, please try again')]"/>
                </x-cards.body>
            </x-cards.card>
        </div>
    @endif
</x-app-layout>
