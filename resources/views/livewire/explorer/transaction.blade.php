<div>
    @if (! $transaction)
    @else
        <div class="card shadow mb-4">
            <div class="card-header border-bottom">
                <div class="d-block d-sm-flex align-items-center">
                    <div class="flex-grow-1 mb-2 mb-md-0">
                        <div class="text-muted d-flex justify-content-between">
                            Transaction
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
                                        class="bi {{ $transaction->is_favourited ? 'bi-star-fill' : 'bi-star' }} hover-text"
                                        data-bs-toggle="tooltip"
                                        data-bs-title="{{ $transaction->is_favourited ? 'Edit' : 'Add' }} Favorite"
                                        wire:click="$emit('showModal', 'modals.explorer.manage-favorite-transaction', '{{ $transaction->hash }}')"
                                    ></i>
                                @endif
                                <i class="bi bi-clipboard ms-2 hover-text js-copy" data-clipboard-text="{{ $transaction->hash }}" data-bs-toggle="tooltip" data-bs-title="Copy hash"></i>
                            </span>
                        </div>
                        <h5 class="card-title mb-0 flex-grow-1"># {{ $transaction->display_height }}</h5>
                    </div>
                    <div class="d-block d-md-flex justify-content-md-end">
                        <div class="w-100">
                            <nav class="align-items-center p-2 bg-secondary rounded-2 border border-light border-1">
                                <ul class="pagination justify-content-between">
                                    {{-- Previous Page Link --}}
                                    @if ($transaction->previous_block)
                                        <li class="page-item">
                                            <a
                                                class="page-link"
                                                id="transaction-pagination-{{$transaction->height}}-previous"
                                                wire:click="loadTransaction('{{ $transaction->previous_block->hash }}')"
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
                                    @if ($transaction->next_block)
                                        <li class="page-item">
                                            <a
                                                class="page-link"
                                                id="transaction-pagination-{{$transaction->height}}-next"
                                                wire:click="loadTransaction('{{ $transaction->next_block->hash }}')"
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
                        <div class="bg-secondary shadow rounded-3 mb-2 p-3">
                            <div class="d-block d-md-flex justify-content-md-evenly">
                                <div class="text-start text-md-center mb-2 mb-md-0">
                                    <span class="d-inline d-md-block text-muted fs-sm">From</span>
                                    <span class="float-end float-md-none"><x-address :eitherSide="8" :alwaysShort="true" :account="$transaction->account"/></span>
                                </div>
                                <div class="d-none d-md-block text-md-center align-self-center">
                                    {!! svg('explorer/send', 'text-success', 'transform: rotate(90deg);') !!}
                                </div>
                                <div class="text-start text-md-center mb-2 mb-md-0">
                                    <span class="d-inline d-md-block text-muted fs-sm">To</span>
                                    <span class="float-end float-md-none"><x-address :eitherSide="8" :alwaysShort="true" :account="$transaction->to_account"/></span>
                                </div>
                            </div>
                            <div class="d-block d-md-flex justify-content-md-evenly mt-2 pt-0 border-1 border-top-md mt-md-4 pt-md-4">
                                @if ($transaction->is_un_received)
                                    <div class="text-start text-md-center mb-2 mb-md-0">
                                        <span class="d-inline d-md-block text-muted fs-sm">Unreceived</span>
                                        <span class="float-end float-md-none">
                                            {!! svg('explorer/unreceived', 'text-danger') !!}
                                        </span>
                                    </div>
                                @endif
                                <div class="text-start text-md-center mb-2 mb-md-0">
                                    <span class="d-inline d-md-block text-muted fs-sm">Confirmations</span>
                                    <span class="float-end float-md-none">
                                        @if ($transaction->raw_json?->confirmationDetail?->numConfirmations)
                                            {{ number_format($transaction->raw_json->confirmationDetail->numConfirmations) }}
                                        @else
                                            -
                                        @endif
                                    </span>
                                </div>
                                <div class="text-start text-md-center mb-2 mb-md-0">
                                    <span class="d-inline d-md-block text-muted fs-sm">Type</span>
                                    <span class="float-end float-md-none">
                                        {{ ($transaction->display_type ?: $transaction->display_actual_type) }}
                                    </span>
                                </div>
                                <div class="text-start text-md-center">
                                    <span class="d-inline d-md-block text-muted fs-sm">Amount</span>
                                    <span class="float-end float-md-none">
                                        @if ($transaction->token && $transaction->amount > 0)
                                            {{ $transaction->display_amount }}
                                            <a href=" {{ route('explorer.token', ['zts' => $transaction->token->token_standard]) }}">
                                                {{ $transaction->token->symbol }}
                                            </a>
                                        @else
                                            -
                                        @endif
                                    </span>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
                <ul class="list-group list-group-flush mb-0">
                    <li class="list-group-item">
                        <span class="d-block fs-sm text-muted">Timestamp</span>
                        {{ $transaction->created_at->format(config('zenon.date_format')) }}
                    </li>
                    <li class="list-group-item">
                        <span class="d-block fs-sm text-muted">Hash</span>
                        <x-hash-tooltip :hash="$transaction->hash"/>
                    </li>
                    <li class="list-group-item">
                        <span class="d-block fs-sm text-muted">Data</span>
                        @if ($transaction->data)
                            @if ($transaction->contract_method_id)
                                {{ $transaction->contract_method->name }} ({{ $transaction->contract_method->contract->name }})
                                <pre class="line-numbers mt-2 mb-3"><code class="lang-json">{{ $transaction->data->json }}</code></pre>
                            @endif
                            Raw
                            <pre class="line-numbers mt-2">{{ $transaction->data->raw }}</pre>
                        @else
                            -
                        @endif
                    </li>
                    <li class="list-group-item">
                        <span class="d-block fs-sm text-muted">Momentum</span>
                        @if ($transaction->momentum)
                            <a href="{{ route('explorer.momentum', ['hash' => $transaction->momentum->hash]) }}">
                                <x-hash-tooltip :hash="$transaction->momentum->hash"/>
                            </a>
                        @else
                            -
                        @endif
                    </li>
                    <li class="list-group-item">
                        <span class="d-block fs-sm text-muted">Paired account block</span>
                        @if ($transaction->paired_account_block)
                            <a href=" {{ route('explorer.transaction', ['hash' => $transaction->paired_account_block->hash]) }}">
                                <x-hash-tooltip :hash="$transaction->paired_account_block->hash"/>
                            </a>
                        @else
                            -
                        @endif
                    </li>
                    <li class="list-group-item">
                        <span class="d-block fs-sm text-muted">Parent</span>
                        @if ($transaction->parent)
                            <a href=" {{ route('explorer.transaction', ['hash' => $transaction->parent->hash]) }}">
                                <x-hash-tooltip :hash="$transaction->parent->hash"/>
                            </a>
                        @else
                            -
                        @endif
                    </li>
                </ul>
            </div>
        </div>
        <div class="card shadow">
            <div class="card-header">
                <div class="d-md-none">
                    <select id="transaction-sections" class="form-select" wire:change="$set('tab', $event.target.value)">
                        <option value="descendants" {{ $tab === 'descendants' ? 'selected' : '' }}>Descendants</option>
                        <option value="json" {{ $tab === 'json' ? 'selected' : '' }}>JSON</option>
                    </select>
                </div>
                <div class="d-none d-md-block">
                    <ul class="nav nav-tabs-alt card-header-tabs">
                        <li class="nav-item">
                            <button class="btn nav-link {{ $tab === 'descendants' ? 'active' : '' }}" wire:click="$set('tab', 'descendants')">
                                Descendants
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
                    @if ($tab === 'descendants')
                        <livewire:tables.block-descendants :transaction="$transaction" key="{{now()}}" />
                    @elseif ($tab === 'json')
                        <div class="p-4">
                            @if ($transaction->raw_json)
                                <pre class="line-numbers"><code class="lang-json">{{ pretty_json($transaction->raw_json) }}</code></pre>
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
