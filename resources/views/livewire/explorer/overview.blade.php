<div>
    <div wire:poll.10000ms>
        <div class="card shadow mb-4">
            <div class="card-header border-0">
                <div class="row">
                    <div class="col-24">
                        <livewire:explorer.search key="{{now()}}" />
                    </div>
                </div>
                <div class="row text-center">
                    @foreach($stats as $stat => $value)
                        <div class="col-12 col-md-8 col-lg-4 mt-3">
                            <div class="p-1 bg-faded-light rounded-2">
                                <span class="d-block fs-sm">{{ Str::ucfirst($stat) }}</span>
                                <span class="fw-bold">
                                    {{ $value }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-24 col-lg-12 mb-4">
                <div class="card shadow">
                    <div class="card-header d-flex align-items-center">
                        <h5 class="card-title flex-grow-1 mb-0">
                            <i class="bi bi-box me-2"></i>
                            Momentums
                        </h5>
                        <a href="{{ route('explorer.momentums') }}" class="btn btn-sm btn-outline-secondary float-end">
                            All
                            <i class="bi bi-arrow-right ms-2"></i>
                        </a>
                    </div>
                    <div class="card-body py-2">
                        <div class="list-group list-group-flush mb-0 fw-bold">
                            @foreach($momentums as $momentum)
                                <div class="list-group-item flex-column align-items-start py-3">
                                    <div class="d-flex flex-wrap w-100 justify-content-between">
                                        <div class="text-white mb-2 mb-sm-0">
                                            <a class="d-block" href="{{ route('explorer.momentum', ['hash' => $momentum->hash]) }}">
                                                <x-hash-tooltip :hash="$momentum->hash" :eitherSide="8" :alwaysShort="true" />
                                            </a>
                                            <span class="fs-xs opacity-50">
                                                {{ $momentum->created_at->diffForHumans(['parts' => 2]) }}
                                            </span>
                                        </div>
                                        <div class="text-white text-end d-inline">
                                            <span class="d-block opacity-75">
                                                # {{ $momentum->display_height }}
                                            </span>
                                            <span class="fs-xs opacity-50">
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
            <div class="col-24 col-lg-12">
                <div class="card shadow">
                    <div class="card-header d-flex align-items-center">
                        <h5 class="card-title flex-grow-1 mb-0">
                            <i class="bi bi-arrow-left-right me-2"></i>
                            Transactions
                        </h5>
                        <a href="{{ route('explorer.transactions') }}" class="btn btn-sm btn-outline-secondary float-end">
                            All
                            <i class="bi bi-arrow-right ms-2"></i>
                        </a>
                    </div>
                    <div class="card-body py-2">
                        <div class="list-group list-group-flush mb-0 fw-bold">
                            @foreach($transactions as $block)
                                <div class="list-group-item flex-column align-items-start py-3">
                                    <div class="d-block d-sm-flex flex-wrap w-100 justify-content-sm-between mb-2">
                                        <div class="text-white mb-2 mb-sm-0">
                                            <a class="d-block" href="{{ route('explorer.transaction', ['hash' => $block->hash]) }}">
                                                <x-hash-tooltip :hash="$block->hash" :eitherSide="8" :alwaysShort="true" />
                                            </a>
                                            <span class="fs-xs opacity-50">
                                                {{ $block->created_at->diffForHumans(['parts' => 2]) }}
                                            </span>
                                        </div>
                                        <div class="text-white text-start text-sm-end d-block d-md-inline">
                                            <span class="d-block">
                                                <x-address :account="$block->account" :eitherSide="8" :alwaysShort="true" /> <i class="bi bi-arrow-up ms-1 opacity-50" data-bs-toggle="tooltip" data-bs-title="From"></i>
                                            </span>
                                            <span class="d-block">
                                                <x-address :account="$block->to_account" :eitherSide="8" :alwaysShort="true" /> <i class="bi bi-arrow-down ms-1 opacity-50" data-bs-toggle="tooltip" data-bs-title="To"></i>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="d-inline badge bg-secondary mt-2">
                                        {{ $block->display_type }}
                                    </div>
                                    @if($block->token)
                                        <div class="d-inline ms-2 mt-2 badge bg-secondary">
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
