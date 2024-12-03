<x-app-layout>
    <x-includes.header :responsive-border="false">
        <div class="d-flex justify-content-between mb-4">
            <div class="d-flex align-items-start flex-column">
                <span class="text-muted text-xs">{{ __('Transaction') }}</span>
                <div class="d-flex align-items-center mb-1">
                    <x-includes.header-title title="# {{ $transaction->display_height }}" />
                </div>
            </div>
            <div class="dropdown">
                <button class="btn btn-neutral btn-xs dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-three-dots"></i>
                </button>
                <ul class="dropdown-menu">
                    <li>
                        <a class="dropdown-item" href="#">
                            <i class="bi bi-share-fill me-2"></i> {{ __('Share') }}
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </x-includes.header>

    <div class="container-fluid px-3 px-md-6">

        <x-cards.card class="mb-6 gy-6">
            <x-cards.body>
                <div class="d-md-flex align-items-center justify-content-start justify-content-md-between">
                    <div class="col-24 col-md-10 text-center">
                        <span class="text-muted text-sm d-block">{{ __('From') }}</span>
                        <x-address :account="$transaction->account" :always-short="true" :copyable="true" />
                    </div>
                    <div class="col-24 col-md-4 text-center py-4 py-md-0">
                        @if($transaction->is_received)
                            <span class="d-none d-md-block">
                                <x-svg file="explorer/send" class="text-success" style="transform: rotate(90deg); "/>
                            </span>
                            <span class="d-block d-md-none">
                                <x-svg file="explorer/send" class="text-success" style="transform: rotate(180deg); "/>
                            </span>
                        @else
                            <span data-bs-toggle="tooltip" data-bs-title="{{ __('Unreceived') }}">
                                <x-svg file="explorer/unreceived" class="text-danger "/>
                            </span>
                        @endif
                    </div>
                    <div class="col-24 col-md-10 text-center">
                        <span class="text-muted text-sm d-block">{{ __('To') }}</span>
                        <x-address :account="$transaction->toAccount" :always-short="true" :copyable="true" />
                    </div>
                </div>
            </x-cards.body>
        </x-cards.card>

        <x-cards.card class="mb-6">
            <x-cards.body>
                <h6 class="mb-1 text-muted">{{ __('Hash') }}</h6>
                <p>{{ $transaction->hash }} <i class="bi bi-clipboard ms-1 js-copy" data-clipboard-text="{{ $transaction->hash }}" data-bs-toggle="tooltip" data-bs-title="Copy"></i></p>
                <hr>
                <div class="row">
                    <div class="col-24 col-lg-12">
                        <div class="vstack gap-3">
                            <x-stats.list-item :title="__('Timestamp')">
                                <x-date-time.carbon :date="$transaction->created_at" />
                            </x-stats.list-item>
                            <x-stats.list-item :title="__('Received')">
                                @if($transaction->pairedAccountBlock)
                                    <x-date-time.carbon :date="$transaction->pairedAccountBlock->created_at" />
                                @else
                                    {{ __('Unreceived') }}
                                @endif
                            </x-stats.list-item>
                            <x-stats.list-item :title="__('Amount')">
                                @if ($transaction->token && $transaction->amount > 0)
                                    {{ $transaction->display_amount }}
                                    <x-link :href="route('explorer.token.detail', ['zts' => $transaction->token->token_standard])">
                                        {{ $transaction->token->symbol }}
                                    </x-link>
                                @else
                                    -
                                @endif
                            </x-stats.list-item>
                            <x-stats.list-item :title="__('Type')" :stat="$transaction->display_actual_type" :hr="false" />
                            <hr class="d-block d-lg-none my-0 mb-3">
                        </div>
                    </div>
                    <div class="col-24 col-lg-12">
                        <div class="vstack gap-3">
                            <x-stats.list-item :title="__('Confirmations')">
                                @if ($transaction->raw_json?->confirmationDetail?->numConfirmations)
                                    {{ number_format($transaction->raw_json->confirmationDetail->numConfirmations) }}
                                @else
                                    -
                                @endif
                            </x-stats.list-item>
                            <x-stats.list-item :title="__('Momentum')">
                                <x-hash :hash="$transaction->momentum->hash"  :always-short="true" :copyable="true" :link="route('explorer.momentum.detail', ['hash' => $transaction->momentum->hash])"/>
                            </x-stats.list-item>
                            <x-stats.list-item :title="__('Paired Block')">
                                @if($transaction->pairedAccountBlock)
                                    <x-hash :hash="$transaction->pairedAccountBlock->hash" :always-short="true" :copyable="true" :link="route('explorer.transaction.detail', ['hash' => $transaction->pairedAccountBlock->hash])"/>
                                @else
                                    -
                                @endif
                            </x-stats.list-item>
                            <x-stats.list-item :title="__('Parent Block')" :hr="false">
                                @if($transaction->parent)
                                    <x-hash :hash="$transaction->parent->hash" :always-short="true" :copyable="true" :link="route('explorer.transaction.detail', ['hash' => $transaction->parent->hash])"/>
                                @else
                                    -
                                @endif
                            </x-stats.list-item>
                        </div>
                    </div>
                </div>
            </x-cards.body>
        </x-cards.card>
    </div>


    <x-includes.header>
        <x-navigation.header.responsive-nav :items="[
            __('Data') => route('explorer.transaction.detail', ['hash' => $transaction->hash, 'tab' => 'data']),
            __('Descendants') => route('explorer.transaction.detail', ['hash' => $transaction->hash, 'tab' => 'descendants']),
            __('JSON') => route('explorer.transaction.detail', ['hash' => $transaction->hash, 'tab' => 'json']),
        ]" :active="$tab" />
    </x-includes.header>

    @if ($tab === 'data')
        <div class="mx-3 mx-md-6 mb-4">
            @if($transaction->data)
                <x-cards.card>
                    <x-cards.body>
                        <h4 class="mb-3">{{ __('Raw') }}</h4>
                        <pre class="line-numbers mb-0 p-4 border rounded bg-body-tertiary shadow text-wrap">{{ $transaction->data->raw }}</pre>
                        <hr class="my-6">
                        <h4 class="mb-3">{{ __('Decoded') }}</h4>
                        <pre class="line-numbers mb-0 p-4 border rounded bg-body-tertiary shadow"><code class="lang-json">{{ json_encode($transaction->data->decoded, JSON_PRETTY_PRINT) }}</code></pre>
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
        <livewire:explorer.transaction.descendants-list :transactionId="$transaction->id" />
    @endif

    @if ($tab === 'json')
        <div class="mx-3 mx-md-6">
            <x-code-highlighters.json :code="$transaction->raw_json" />
        </div>
    @endif
</x-app-layout>
