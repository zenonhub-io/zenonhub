<div>
    <x-cards.card>
        <x-cards.body>
            <div class="row justify-content-between align-items-center">
                <div class="col-24 col-lg">
                    <h4>{{ __('Inbound Vs Outbound') }}</h4>
                    <div class="d-block text-muted text-sm">
                        <x-date-time.carbon :date="$dateRange->first()" format="jS M Y" :show-tooltip="false" class="d-inline" /> - <x-date-time.carbon :date="$dateRange->last()" format="jS M Y" :show-tooltip="false" class="d-inline" />
                    </div>
                </div>
                <div class="col-24 col-lg-auto">
                    <div class="d-flex justify-content-between gap-1 p-1 align-items-center bg-body-secondary rounded text-xs fw-semibold mt-3 mt-lg-0">
                        <div class="dropdown">
                            <button class="btn btn-neutral btn-xs dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                {{ Str::upper($token) }}
                            </button>
                            <ul class="dropdown-menu">
                                @foreach([
                                    'znn' => 'ZNN',
                                    'qsr' => 'QSR',
                                ] as $tokenKey => $tokenTitle)
                                    <li><a class="dropdown-item {{ $token === $tokenKey ? 'active' : '' }}" href="#" wire:click="$set('token','{{ $tokenKey }}')">{{ $tokenTitle }}</a></li>
                                @endforeach
                            </ul>
                        </div>
                        @foreach([
                            '7d' => '7D',
                            '30d' => '30D',
                            'w' => 'W',
                            'm' => 'M',
                            'y' => 'Y',
                        ] as $timeframeKey => $timeframeTitle)
                            <a href="#"
                               class="px-3 py-1 text-muted {{ $timeframeKey === $timeframe ? 'bg-dark' : 'bg-dark-hover bg-opacity-70-hover' }} rounded"
                               wire:click="$set('timeframe','{{ $timeframeKey }}')"
                            >
                                {{ __($timeframeTitle) }}
                            </a>
                        @endforeach
                        <a href="#"
                           class="px-3 py-1 text-muted bg-dark-hover bg-opacity-70-hover rounded"
                           wire:click="previousDate()"
                        >
                            <i class="bi bi-chevron-left"></i>
                        </a>
                        <a href="#"
                           class="px-3 py-1 text-muted bg-dark-hover bg-opacity-70-hover rounded"
                           wire:click="nextDate()"
                        >
                            <i class="bi bi-chevron-right"></i>
                        </a>
                    </div>
                </div>
                <div class="col-24">
                    <hr class="my-4">
                </div>
            </div>
            <div class="row">
                <div class="col-24 col-sm-12">
                    <div class="vstack gap-3">
                        <x-stats.list-item :title="__('Volume')" :stat="$totalVolume" breakpoint="sm"/>
                        <x-stats.list-item :title="__('Inbound TX')" :stat="$inboundTx" breakpoint="sm"/>
                        <x-stats.list-item :title="__('Inbound Amount')" :stat="$inboundAmount" :hr="false" breakpoint="sm"/>
                        <hr class="d-block d-sm-none my-0 mb-3">
                    </div>
                </div>
                <div class="col-24 col-sm-12">
                    <div class="vstack gap-3">
                        <x-stats.list-item :title="__('Net Flow')" :stat="$netFlow" breakpoint="sm"/>
                        <x-stats.list-item :title="__('Outbound TX')" :stat="$outboundTx" breakpoint="sm"/>
                        <x-stats.list-item :title="__('Outbound Amount')" :stat="$outboundAmount" :hr="false" breakpoint="sm"/>
                    </div>
                </div>
            </div>
            <div class="mx-n4 h-100">
                <livewire:livewire-column-chart
                    key="{{ $chartData->reactiveKey() }}"
                    :column-chart-model="$chartData"
                />
            </div>
        </x-cards.body>
    </x-cards.card>
</div>
