<div>
    <x-cards.card>
        <x-cards.body>
            <x-stats.mini-stat :title="__('Total Addresses')">
                {{ $total }}
            </x-stats.mini-stat>
            <div class="bg-dark-subtle rounded border mt-4">
                <div class="p-4">
                    <h6 class="text-muted">{{ __('Active Today') }}</h6>
                    <div class="d-block text-wrap lead text-wrap text-break">
                        {{ $dailyActive }}
                    </div>
                    <hr class="my-4">
                    <h6 class="text-muted">{{ __('Created Today') }}</h6>
                    <div class="d-block text-wrap lead text-wrap text-break">
                        {{ $dailyCreated }}
                    </div>
                </div>
            </div>
        </x-cards.body>
    </x-cards.card>
</div>
