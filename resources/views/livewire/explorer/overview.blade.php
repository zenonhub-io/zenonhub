<div>
    <div wire:poll.10000ms>
        <div class="card shadow mb-4">
            <div class="card-header border-0">
                <div class="row">
                    <div class="col-24">
                        <livewire:explorer.search key="{{now()}}" />
                    </div>
                </div>
                <div class="row">
                    @foreach($stats as $stat => $value)
                        <div class="col-12 col-md-8 col-lg-4 mt-3">
                            <div class="p-1 bg-secondary rounded-2 text-center">
                                <span class="d-block fs-sm text-muted">{{ Str::ucfirst($stat) }}</span>
                                {{ $value }}
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-24 col-md-12 mb-4">
                <div class="card shadow">
                    <div class="card-header d-flex align-items-center">
                        <h5 class="card-title flex-grow-1 mb-0">
                            <i class="bi bi-box me-2"></i>
                            Momentums
                        </h5>
                        <a href="{{ route('explorer.momentums') }}" class="btn btn-sm btn-outline-primary float-end">
                            All
                            <i class="bi bi-arrow-right ms-2"></i>
                        </a>
                    </div>
                    <div class="card-body py-2">
                        <div class="list-group list-group-flush mb-0">
                            @foreach($momentums as $momentum)
                                <div class="list-group-item flex-column align-items-start py-3">
                                    <div class="d-flex flex-wrap w-100 justify-content-between">
                                        <div class="mb-2 mb-sm-0">
                                            <a class="d-block" href="{{ route('explorer.momentum', ['hash' => $momentum->hash]) }}">
                                                <x-hash-tooltip :hash="$momentum->hash" :eitherSide="7" :alwaysShort="true" />
                                            </a>
                                            <span class="fs-xs text-muted">
                                                {{ $momentum->created_at->diffForHumans(['parts' => 2]) }}
                                            </span>
                                        </div>
                                        <div class="text-end d-inline">
                                            <span class="d-block">
                                                # {{ $momentum->display_height }}
                                            </span>
                                            <span class="fs-xs text-muted">
                                                {{ $momentum->account_blocks_count }} account {{ Str::plural('block', $momentum->account_blocks_count) }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-24 col-md-12">
                <div class="card shadow">
                    <div class="card-header d-flex align-items-center">
                        <h5 class="card-title flex-grow-1 mb-0">
                            <i class="bi bi-arrow-down-up me-2"></i>
                            Transactions
                        </h5>
                        <a href="{{ route('explorer.transactions') }}" class="btn btn-sm btn-outline-primary float-end">
                            All
                            <i class="bi bi-arrow-right ms-2"></i>
                        </a>
                    </div>
                    <div class="card-body py-2">
                        <div class="list-group list-group-flush mb-0">
                            @foreach($transactions as $block)
                                <div class="list-group-item flex-column align-items-start py-3">
                                    <div class="d-flex flex-wrap w-100 justify-content-between mb-2">
                                        <div class="mb-2 mb-sm-0">
                                            <a class="d-block" href="{{ route('explorer.transaction', ['hash' => $block->hash]) }}">
                                                <x-hash-tooltip :hash="$block->hash" :eitherSide="7" :alwaysShort="true" />
                                            </a>
                                            <span class="fs-xs text-muted">
                                                {{ $block->created_at->diffForHumans(['parts' => 2]) }}
                                            </span>
                                        </div>
                                        <div class="text-start text-sm-end d-block d-sm-inline mb-2 my-sm-0">
                                            <span class="d-block mb-2">
                                                <span class="d-inline d-sm-none" data-bs-toggle="tooltip" data-bs-title="Sender">{!! svg('explorer.send', 'text-success') !!}</span>
                                                <x-address :account="$block->account" :eitherSide="6" :alwaysShort="true" />
                                                <span class="d-none d-sm-inline" data-bs-toggle="tooltip" data-bs-title="Sender">{!! svg('explorer.send', 'text-success') !!}</span>
                                            </span>
                                            <span class="d-block">
                                                <span class="d-inline d-sm-none" data-bs-toggle="tooltip" data-bs-title="Receiver">{!! svg('explorer.receive', 'text-warning') !!}</span>
                                                <x-address :account="$block->to_account" :eitherSide="6" :alwaysShort="true" />
                                                <span class="d-none d-sm-inline" data-bs-toggle="tooltip" data-bs-title="Receiver">{!! svg('explorer.receive', 'text-warning') !!}</span>
                                            </span>
                                        </div>
                                    </div>
                                    @if($block->display_type)
                                        <div class="d-inline badge bg-secondary text-muted mt-2">
                                            {{ $block->display_type }}
                                        </div>
                                    @endif
                                    @if($block->token && $block->amount > 0)
                                        <div class="d-inline badge bg-secondary text-muted mt-2 ms-2">
                                            {{ $block->display_amount }} {{ $block->token->symbol }}
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
