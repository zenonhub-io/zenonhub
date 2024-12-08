<div>
    <x-cards.card>
        <x-cards.body>
            <x-stats.mini-stat :title="__('Transactions')">
                {{ $total }}
            </x-stats.mini-stat>
            <div class="bg-dark-subtle rounded border mt-4">
                <div class="p-4">
                    <h6 class="text-muted">{{ __('Daily TX') }}</h6>
                    <div class="d-block text-wrap lead text-wrap text-break">
                        {{ $daily }}
                    </div>
                    <hr class="my-4">
                    <h6 class="text-muted">{{ __('Latest TX') }}</h6>
                    <div class="d-block text-wrap lead text-wrap text-break">
                        <x-hash
                            :hash="$latest->hash"
                            :either-side="5"
                            :always-short="true"
                            :link="route('explorer.transaction.detail', [
                                'hash' => $latest->hash
                            ])"
                        />
                    </div>
                </div>
            </div>
        </x-cards.body>
    </x-cards.card>
</div>
