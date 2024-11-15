<div>
    <x-cards.card>
        <x-cards.body>
            <div class="row justify-content-between align-items-center">
                <div class="col-24 col-lg">
                    <h5>{{ __('Transactions') }}</h5>
                    <div class="d-block text-muted">
                        <x-date-time.carbon :date="$dateRange->first()" format="jS M Y" :show-tooltip="false" class="d-inline" /> - <x-date-time.carbon :date="$dateRange->last()" format="jS M Y" :show-tooltip="false" class="d-inline" />
                    </div>
                </div>
                <div class="col-24 col-lg-auto">
                    <div class="d-flex justify-content-between gap-1 p-1 align-items-center bg-body-secondary rounded text-xs fw-semibold mt-3 mt-lg-0">
                        @foreach([
                            '7d' => '7D',
                            '30d' => '30D',
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
                    </div>
                </div>
                <div class="col-24">
                    <hr class="my-4">
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
