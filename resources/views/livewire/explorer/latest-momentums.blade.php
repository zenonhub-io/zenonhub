<div>
    <x-cards.card>
        <x-cards.heading class="d-flex align-items-center">
            <h4 class="card-title flex-grow-1 mb-0">
                <i class="bi bi-box me-2"></i>
                {{ __('Momentums') }}
            </h4>
            <a href="{{ route('explorer.momentum.list') }}" class="btn btn-sm btn-outline-primary float-end">
                {{ __('All') }}
                <i class="bi bi-arrow-right ms-2"></i>
            </a>
        </x-cards.heading>

        <div class="list-group list-group-flush">
            @foreach($momentums as $momentum)
                <div class="list-group-item d-flex align-items-center px-6">
                    <div class="d-flex flex-wrap w-100 justify-content-between">
                        <div class="mb-2 mb-sm-0">
                            <a class="d-block" href="{{ route('explorer.momentum.detail', ['hash' => $momentum->hash]) }}">
                                <x-hash :hash="$momentum->hash" :eitherSide="7" :alwaysShort="true" />
                            </a>
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
                </div>
            @endforeach
        </div>

    </x-cards.card>
</div>
