<div>
    @if (! $momentum)
    @else
        <div class="card shadow mb-4">
            <div class="card-header border-bottom">
                <div class="d-block d-sm-flex align-items-center">
                    <div class="flex-grow-1 mb-2 mb-md-0">
                        <div class="text-muted d-flex justify-content-between">
                            Momentum
                            <span class="me-3">
                                @if (! auth()->check())
                                    <a href="{{ route('login', ['redirect' => url()->current()]) }}">
                                        <i
                                            class="bi bi-star"
                                            data-bs-toggle="tooltip"
                                            data-bs-title="Add Favorite"
                                        ></i>
                                    </a>
                                @else
                                    <i
                                        class="bi {{ $momentum->is_favourited ? 'bi-star-fill' : 'bi-star' }} hover-text"
                                        data-bs-toggle="tooltip"
                                        data-bs-title="{{ $momentum->is_favourited ? 'Edit' : 'Add' }} Favorite"
                                        wire:click="$emit('showModal', 'modals.explorer.manage-favorite-momentum', '{{ $momentum->hash }}')"
                                    ></i>
                                @endif
                                <i class="bi bi-clipboard ms-2 hover-text js-copy" data-clipboard-text="{{ $momentum->hash }}" data-bs-toggle="tooltip" data-bs-title="Copy hash"></i>
                            </span>
                        </div>
                        <h5 class="card-title mb-0 flex-grow-1"># {{ $momentum->display_height }}</h5>
                    </div>
                    <div class="d-block d-md-flex justify-content-md-end order-0 order-sm-1">
                        <div class="w-100">
                            <nav class="align-items-center p-1 bg-secondary rounded-2 border border-light border-1">
                                <ul class="pagination justify-content-between">
                                    {{-- Previous Page Link --}}
                                    @if ($momentum->previous_momentum)
                                        <li class="page-item">
                                            <a
                                                class="page-link"
                                                id="momentum-pagination-{{$momentum->height}}-previous"
                                                wire:click="loadMomentum('{{ $momentum->previous_momentum->hash }}')"
                                                href="javascript:;"
                                                rel="prev"
                                                aria-label="@lang('pagination.previous')">
                                                <i class="bi bi-chevron-left fs-6 me-1"></i>
                                                Previous
                                            </a>
                                        </li>
                                    @else
                                        <li class="page-item disabled" aria-disabled="true">
                                            <a class="page-link" href="#" tabindex="-1">
                                                <i class="bi bi-chevron-left fs-6 me-1"></i>
                                                Previous
                                            </a>
                                        </li>
                                    @endif

                                    {{-- Next Page Link --}}
                                    @if ($momentum->next_momentum)
                                        <li class="page-item">
                                            <a
                                                class="page-link"
                                                id="momentum-pagination-{{$momentum->height}}-next"
                                                wire:click="loadMomentum('{{ $momentum->next_momentum->hash }}')"
                                                href="javascript:;"
                                                rel="next"
                                                aria-label="@lang('pagination.next')">
                                                Next
                                                <i class="bi bi-chevron-right fs-6 ms-1"></i>
                                            </a>
                                        </li>
                                    @else
                                        <li class="page-item disabled" aria-disabled="true" aria-label="@lang('pagination.next')">
                                            <span class="page-link" aria-hidden="true">
                                                Next
                                                <i class="bi bi-chevron-right fs-6 ms-1"></i>
                                            </span>
                                        </li>
                                    @endif
                                </ul>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-24">
                        <div class="d-block d-md-flex justify-content-md-evenly bg-secondary shadow rounded-3 mb-2 p-3">
                            <div class="text-start text-md-center">
                                <span class="d-inline d-md-block fs-sm text-muted">Transactions</span>
                                <span class="float-end float-md-none">{{ number_format($momentum->account_blocks->count()) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                <ul class="list-group list-group-flush list-group-sm mb-0">
                    <li class="list-group-item">
                        <span class="d-block fs-sm text-muted">Timestamp</span>
                        {{ $momentum->created_at->format(config('zenon.date_format')) }}
                    </li>
                    <li class="list-group-item">
                        <span class="d-block fs-sm text-muted">Hash</span>
                        <x-hash-tooltip :hash="$momentum->hash"/>
                    </li>
                    <li class="list-group-item">
                        <span class="d-block fs-sm text-muted">Producer</span>
                        @if ($momentum->producer_account)
                            <x-address :account="$momentum->producer_account"/>
                        @endif
                        @if ($momentum->producer_pillar)
                            | <a href="{{ route('pillars.detail', ['slug' => $momentum->producer_pillar->slug]) }}">
                                {{ $momentum->producer_pillar->name }}
                            </a>
                        @endif
                    </li>
                </ul>
            </div>
        </div>
        <div class="card shadow">
            <div class="card-header">
                <div class="d-md-none">
                    <select id="momentum-sections" class="form-control" wire:change="$set('tab', $event.target.value)">
                        <option value="transactions" {{ $tab === 'transactions' ? 'selected' : '' }}>Transactions</option>
                        <option value="json" {{ $tab === 'json' ? 'selected' : '' }}>JSON</option>
                    </select>
                </div>
                <div class="d-none d-md-block">
                    <ul class="nav nav-tabs-alt card-header-tabs">
                        <li class="nav-item">
                            <button class="btn nav-link {{ $tab === 'transactions' ? 'active' : '' }}" wire:click="$set('tab', 'transactions')">
                                Transactions
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="btn nav-link {{ $tab === 'json' ? 'active' : '' }}" wire:click="$set('tab', 'json')">
                                JSON
                            </button>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="tab-content">
                <div class="tab-pane fade show active">
                    @if ($tab === 'transactions')
                        <livewire:tables.momentum-blocks :momentum="$momentum" key="{{now()}}" />
                    @elseif ($tab === 'json')
                        <div class="p-4">
                            @if ($momentum->raw_json)
                                <pre class="line-numbers"><code class="lang-json">{{ pretty_json($momentum->raw_json) }}</code></pre>
                            @else
                                <x-alert
                                    message="Unable to load JSON data"
                                    type="info"
                                    icon="info-circle-fill"
                                    class="d-flex mb-0"
                                />
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif
</div>
