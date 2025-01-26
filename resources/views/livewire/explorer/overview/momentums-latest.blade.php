<div wire:poll.10s>
    <x-cards.card>
        <x-cards.heading class="d-flex align-items-center">
            <h4 class="card-title flex-grow-1 mb-0">
                <i class="bi bi-box me-2"></i>
                {{ __('Momentums') }}
            </h4>
            <x-link :href="route('explorer.momentum.list')" class="btn btn-sm btn-outline-primary float-end">
                {{ __('All') }}
                <i class="bi bi-arrow-right ms-2"></i>
            </x-link>
        </x-cards.heading>

        <div class="list-group list-group-flush">
            @foreach($momentums as $momentum)
                <div class="list-group-item px-6">
                    <div class="d-flex justify-content-between">
                        <div class="mb-2 mb-sm-0">
                            <x-link :href="route('explorer.momentum.detail', ['hash' => $momentum->hash])" class="d-block">
                                <x-hash :hash="$momentum->hash" :eitherSide="7" :alwaysShort="true" />
                            </x-link>
                            <span class="text-xs text-muted">
                                <x-date-time.carbon :date="$momentum->created_at" :human="true" />
                            </span>
                        </div>
                        <div class="text-end d-inline">
                            <span class="d-block">
                                # {{ $momentum->display_height }}
                            </span>
                            <span class="text-xs text-muted">
                                {{ $momentum->account_blocks_count }} account {{ Str::plural('block', $momentum->account_blocks_count) }}
                            </span>
                        </div>
                    </div>
                    @if($momentum->producerPillar)
                        <div class="badge bg-light-subtle text-muted mt-2">
                            {{ $momentum->producerPillar->name }}
                        </div>
                    @endif
                </div>
            @endforeach
        </div>

    </x-cards.card>
</div>
