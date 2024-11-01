<div>
    <x-cards.card>
        <x-cards.body>
            <div class="row g-3 justify-content-between align-items-center">
                <div class="col-24 col-lg">
                    <h5>{{ __('Inbound Vs Outbound TX') }}</h5>
                    <div class="d-block text-muted">
                        <x-date-time.carbon :date="$startDate" format="jS M Y" :show-tooltip="false" class="d-inline" /> - <x-date-time.carbon :date="$endDate" format="jS M Y" :show-tooltip="false" class="d-inline" />
                    </div>
                </div>
                <div class="col-24 col-lg-auto">
                    <div class="d-flex justify-content-between gap-1 p-1 align-items-center bg-body-secondary rounded text-xs fw-semibold">
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
                            'd' => 'D',
                            'w' => 'W',
                            'm' => 'M',
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
            </div>
            <div class="mx-n4 h-100">
                <livewire:livewire-column-chart
                    key="{{ $chartModel->reactiveKey() }}"
                    :column-chart-model="$chartModel"
                />
            </div>
        </x-cards.body>
    </x-cards.card>
</div>
