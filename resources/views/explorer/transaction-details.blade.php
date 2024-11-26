<x-app-layout>
    <x-includes.header :responsive-border="false">
        <div class="d-flex justify-content-between mb-4">
            <div class="d-flex align-items-start flex-column">
                <span class="text-muted text-xs">{{ __('Transaction') }}</span>
                <div class="d-flex align-items-center mb-1">
                    <x-includes.header-title title="# {{ number_format($transaction->height) }}" />
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

    <div class="container-fluid px-3 px-md-8">

        <x-cards.card class="mb-6 gy-6">
            <x-cards.body>
                <div class="d-md-flex align-items-center justify-content-start justify-content-md-between">
                    <div class="col-24 col-md-10 text-center">
                        <span class="text-muted text-sm d-block">{{ __('From') }}</span>
                        <x-address :account="$transaction->account" :always-short="true" />
                    </div>
                    <div class="col-24 col-md-4 text-center py-4 py-md-0">
                        @if($transaction->is_received)
                            <x-svg file="explorer/send" class="text-success" style="transform: rotate(90deg);"/>
                        @else
                            <span data-bs-toggle="tooltip" data-bs-title="{{ __('Unreceived') }}">
                                <x-svg file="explorer/unreceived" class="text-danger"/>
                            </span>
                        @endif
                    </div>
                    <div class="col-24 col-md-10 text-center">
                        <span class="text-muted text-sm d-block">{{ __('To') }}</span>
                        <x-address :account="$transaction->toAccount" :always-short="true" />
                    </div>
                </div>
            </x-cards.body>
        </x-cards.card>


        <div class="row mb-6 gy-6">
            <div class="col-12 col-md-8">
                <x-cards.card>
                    <x-cards.body>
                        <x-stats.mini-stat
                            :title="__('Confirmations')"
                            :info="__('Number of momentums since confirmed')">
                            @if ($transaction->raw_json?->confirmationDetail?->numConfirmations)
                                {{ number_format($transaction->raw_json->confirmationDetail->numConfirmations) }}
                            @else
                                -
                            @endif
                        </x-stats.mini-stat>
                    </x-cards.body>
                </x-cards.card>
            </div>
            <div class="col-12 col-md-8">
                <x-cards.card>
                    <x-cards.body>
                        <x-stats.mini-stat
                            :title="__('Type')"
                            :info="__('The transaction type')">
                            {{ ($transaction->display_type ?: $transaction->display_actual_type) }}
                        </x-stats.mini-stat>
                    </x-cards.body>
                </x-cards.card>
            </div>
            <div class="col-24 col-md-8">
                <x-cards.card>
                    <x-cards.body>
                        <x-stats.mini-stat
                            :title="__('Amount')">
                            @if ($transaction->token && $transaction->amount > 0)
                                {{ $transaction->display_amount }}
                                <x-link :href="route('explorer.token.detail', ['zts' => $transaction->token->token_standard])">
                                    {{ $transaction->token->symbol }}
                                </x-link>
                            @else
                                -
                            @endif
                        </x-stats.mini-stat>
                    </x-cards.body>
                </x-cards.card>
            </div>
        </div>
{{--        <x-cards.card class="mb-6">--}}
{{--            <x-cards.body>--}}
{{--                <div class="row">--}}
{{--                    <div class="col-24 col-lg-12">--}}
{{--                        <div class="vstack gap-3">--}}
{{--                            <x-stats.list-item :title="__('Hash')">--}}
{{--                                <x-hash :hash="$momentum->hash" :always-short="true"/>--}}
{{--                            </x-stats.list-item>--}}
{{--                            <x-stats.list-item :title="__('Created')" :hr="false">--}}
{{--                                <x-date-time.carbon :date="$momentum->created_at" />--}}
{{--                            </x-stats.list-item>--}}
{{--                            <hr class="d-block d-md-none my-0 mb-3">--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                    <div class="col-24 col-lg-12">--}}
{{--                        <div class="vstack gap-3">--}}
{{--                            <x-stats.list-item :title="__('Age')">--}}
{{--                                <x-date-time.carbon :date="$momentum->created_at" :human="true" />--}}
{{--                            </x-stats.list-item>--}}
{{--                            <x-stats.list-item :title="__('Producer')" :hr="false">--}}
{{--                                <x-address :account="$momentum->producerAccount" />--}}
{{--                            </x-stats.list-item>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--            </x-cards.body>--}}
{{--        </x-cards.card>--}}
    </div>

    <x-includes.header>
        <x-navigation.header.responsive-nav :items="[
            __('Data') => route('explorer.transaction.detail', ['hash' => $transaction->hash, 'tab' => 'data']),
            __('Descendants') => route('explorer.transaction.detail', ['hash' => $transaction->hash, 'tab' => 'descendants']),
            __('JSON') => route('explorer.transaction.detail', ['hash' => $transaction->hash, 'tab' => 'json']),
        ]" :active="$tab" />
    </x-includes.header>

    @if ($tab === 'data')
{{--        <livewire:explorer.momentum.transactions-list :transactionId="$transaction->id" />--}}
    @endif

    @if ($tab === 'data')
{{--        <livewire:explorer.momentum.transactions-list :transactionId="$transaction->id" />--}}
    @endif

    @if ($tab === 'json')
        <div class="mx-3 mx-md-6">
            <x-code-highlighters.json :code="$transaction->raw_json" />
        </div>
    @endif
</x-app-layout>
