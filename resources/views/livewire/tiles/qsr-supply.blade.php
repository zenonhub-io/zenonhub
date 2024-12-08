<div>
    <x-cards.card>
        <x-cards.body>
            <x-stats.mini-stat :title="__('QSR Supply')">
                <span class="text-secondary">
                    {{ $total_supply }}
                </span>
            </x-stats.mini-stat>
            <div class="bg-dark-subtle rounded border mt-4">
                <div class="p-4">
                    <h6 class="text-muted">{{ __('Circulating Supply') }}</h6>
                    <div class="d-block text-wrap lead text-wrap text-break">
                        {{ $circulating_supply }}
                    </div>
                    <hr class="my-4">
                    <h6 class="text-muted">{{ __('Locked Supply') }}</h6>
                    <div class="d-block text-wrap lead text-wrap text-break">
                        {{ $locked_supply }}
                    </div>
                </div>
            </div>
        </x-cards.body>
    </x-cards.card>
</div>
