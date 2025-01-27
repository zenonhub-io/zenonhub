<div wire:poll.10s>
    <x-cards.card>
        <x-cards.heading class="d-flex align-items-center">
            <h4 class="card-title flex-grow-1 mb-0">
                <i class="bi bi-arrow-down-up me-2"></i>
                {{ __('Transactions') }}
            </h4>
            <x-link :href="route('explorer.transaction.list')" class="btn btn-sm btn-outline-primary float-end">
                {{ __('All') }}
                <i class="bi bi-arrow-right ms-2"></i>
            </x-link>
        </x-cards.heading>

        <div class="list-group list-group-flush">
            @foreach($transactions as $tx)
                <div class="list-group-item px-6">
                    <div class="d-block d-md-flex justify-content-between">
                        <div class="mb-2 d-flex align-items-center d-md-block">
                            <x-link :href="route('explorer.transaction.detail', ['hash' => $tx->hash])" class="d-block">
                                <x-hash :hash="$tx->hash" :eitherSide="7" :alwaysShort="true" />
                            </x-link>
                            <x-date-time.carbon :date="$tx->created_at" :human="true" class="text-xs text-muted ms-2 ms-md-0" />
                        </div>
                        <div class="d-block mb-3 mb-md-0">
                            <span class="d-flex justify-content-start justify-content-md-end align-items-center mb-1">
                                <span class="text-muted text-sm me-1">
                                    {{ __('From') }}:
                                </span>
                                <x-address :account="$tx->account" :always-short="true" :either-side="6" />
                                <span class="ms-2">
                                    <x-svg file="explorer/send" style="height: 16px" class="text-info"/>
                                </span>
                            </span>
                            <span class="d-flex justify-content-start justify-content-md-end align-items-center">
                                <span class="text-muted text-sm me-1">
                                    {{ __('To') }}:
                                </span>
                                <x-address :account="$tx->toAccount" :always-short="true" :either-side="6" />
                                <span class="ms-2">
                                    <x-svg file="explorer/receive" style="height: 16px" class="text-success" />
                                </span>
                            </span>
                        </div>
                    </div>
                    <div class="badge bg-light-subtle text-muted">
                        {{ $tx->display_actual_type }}
                    </div>
                    @if($tx->token && $tx->amount > 0)
                        <div class="badge bg-light-subtle text-muted mt-2 ms-2">
                            {{ $tx->display_amount }} {{ $tx->token->symbol }}
                        </div>
                    @endif
                </div>
            @endforeach
        </div>

    </x-cards.card>
</div>
