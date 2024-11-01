<div>
    <x-cards.card>
        <x-cards.body>
            <div class="row g-3 justify-content-between align-items-center">
                <div class="col-24 col-sm">
                    <h5>{{ __('Inbound Vs Outbound TX') }}</h5>
                </div>
                <div class="col-24 col-sm-auto">
                    <div class="d-flex justify-content-between gap-1 p-1 align-items-center bg-body-secondary rounded text-xs fw-semibold">
                        <div class="dropdown">
                            <button class="btn btn-neutral btn-xs dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                {{ Str::upper($token) }}
                            </button>
                            <ul class="dropdown-menu">
                                @foreach([
                                    'all' => 'All',
                                    'znn' => 'ZNN',
                                    'qsr' => 'QSR',
                                ] as $tokenKey => $tokenTitle)
                                    <li><a class="dropdown-item {{ $token === $tokenKey ? 'active' : '' }}" href="#" wire:click="$set('token','{{ $tokenKey }}')">{{ $tokenTitle }}</a></li>
                                @endforeach
                            </ul>
                        </div>

                        @foreach([
                            '1h' => '1H',
                            '1d' => '1D',
                            '1w' => '1W',
                            '1m' => '1M',
                            '1y' => '1Y',
                        ] as $timeframeKey => $timeframeTitle)
                            <a href="#"
                               class="px-3 py-1 text-muted {{ $timeframeKey === $timeframe ? 'bg-dark' : 'bg-dark-hover bg-opacity-70-hover' }} rounded"
                               wire:click="$set('timeframe','{{ $timeframeKey }}')"
                            >
                                {{ __($timeframeTitle) }}
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="mx-n4 h-100">
                <livewire:livewire-pie-chart
                    key="{{ $pieChartModel->reactiveKey() }}"
                    :pie-chart-model="$pieChartModel"
                />
            </div>
        </x-cards.body>
    </x-cards.card>
</div>
