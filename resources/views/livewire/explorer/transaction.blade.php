<div>
    @if (! $transaction)
    @else
        <div class="card shadow mb-4">
            <div class="card-header">
                <div class="d-block d-sm-flex align-items-center">
                    <div class="flex-grow-1 mb-2 mb-md-0">
                        <div class="text-muted fs-sm">
                            Transaction
                        </div>
                        <h5 class="card-title mb-0 flex-grow-1"># {{ $transaction->display_height }}</h5>
                    </div>
                    <div class="d-block d-md-flex justify-content-md-end">
                        <div class="w-100">
                            <nav class="align-items-center p-1 bg-secondary rounded-2">
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
                        <div class="d-block d-md-flex justify-content-md-evenly bg-secondary shadow rounded-2 mb-2 p-3">
                            <div class="text-start text-md-center mb-2 mb-md-0">
                                <span class="d-inline d-md-block fs-sm text-muted">Type</span>
                                <span class="fw-bold float-end float-md-none">{{ $transaction->display_type }}</span>
                            </div>
                            <div class="text-start text-md-center mb-2 mb-md-0">
                                <span class="d-inline d-md-block fs-sm text-muted">Amount</span>
                                <span class="fw-bold float-end float-md-none">
                                    @if ($transaction->token)
                                        {{ $transaction->display_amount }} {{ $transaction->token->symbol }}
                                    @else
                                        -
                                    @endif
                                </span>
                            </div>
                            <div class="text-start text-md-center">
                                <span class="d-inline d-md-block fs-sm text-muted">Confirmations</span>
                                <span class="fw-bold float-end float-md-none">{{ number_format($transaction->raw_json->confirmationDetail->numConfirmations) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                <ul class="list-group list-group-flush mb-0">
                    <li class="list-group-item">
                        <span class="d-block fs-sm">Timestamp</span>
                        <span class="fw-bold">
                            {{ $transaction->created_at->format(config('zenon.date_format')) }}
                        </span>
                    </li>
                    <li class="list-group-item">
                        <span class="d-block fs-sm">Hash</span>
                        <span class="fw-bold">
                            <x-hash-tooltip :hash="$transaction->hash"/>
                        </span>
                    </li>
                    <li class="list-group-item">
                        <span class="d-block fs-sm">Token</span>
                        <span class="fw-bold">
                            @if ($transaction->token)
                                <a href=" {{ route('explorer.token', ['zts' => $transaction->token->token_standard]) }}">
                                    {{ $transaction->token->name }}
                                </a>
                            @else
                                -
                            @endif
                        </span>
                    </li>
                    <li class="list-group-item">
                        <span class="d-block fs-sm">Method</span>
                        <span class="fw-bold">
                            @if ($transaction->contract_method)
                                {{ $transaction->contract_method->name }} ({{ $transaction->contract_method->contract->name }})
                            @else
                                -
                            @endif
                        </span>
                    </li>
                    <li class="list-group-item">
                        <span class="d-block fs-sm">Data</span>
                        <span class="fw-bold">
                            @if ($transaction->data)
                                @if ($transaction->data->contract_method_id)
                                    <pre class="line-numbers mt-2"><code class="lang-json">{{ $transaction->data->json }}</code></pre>
                                @elseif (! in_array($transaction->data->raw, ['AAAAAAAAAAI=', 'AAAAAAAAAAE=', 'IAk+pg==']))
                                    <pre class="line-numbers mt-2">{{ base64_decode($transaction->data->raw) }}</pre>
                                @else
                                    -
                                @endif
                            @else
                                -
                            @endif
                        </span>
                    </li>
                    <li class="list-group-item">
                        <span class="d-block fs-sm">From</span>
                        <span class="fw-bold">
                            <x-address :account="$transaction->account"/>
                        </span>
                    </li>
                    <li class="list-group-item">
                        <span class="d-block fs-sm">To</span>
                        <span class="fw-bold">
                            <x-address :account="$transaction->to_account"/>
                        </span>
                    </li>
                    <li class="list-group-item">
                        <span class="d-block fs-sm">Momentum</span>
                        <span class="fw-bold">
                            <a href="{{ route('explorer.momentum', ['hash' => $transaction->momentum->hash]) }}">
                                <x-hash-tooltip :hash="$transaction->momentum->hash"/>
                            </a>
                        </span>
                    </li>
                    <li class="list-group-item">
                        <span class="d-block fs-sm">Paired account block</span>
                        <span class="fw-bold">
                            @if ($transaction->paired_account_block)
                                <a href=" {{ route('explorer.transaction', ['hash' => $transaction->paired_account_block->hash]) }}">
                                    <x-hash-tooltip :hash="$transaction->paired_account_block->hash"/>
                                </a>
                            @else
                                -
                            @endif
                        </span>
                    </li>
                    <li class="list-group-item">
                        <span class="d-block fs-sm">Parent</span>
                        <span class="fw-bold">
                            @if ($transaction->parent)
                                <a href=" {{ route('explorer.transaction', ['hash' => $transaction->parent->hash]) }}">
                                    <x-hash-tooltip :hash="$transaction->parent->hash"/>
                                </a>
                            @else
                                -
                            @endif
                        </span>
                    </li>
                </ul>
            </div>
        </div>
        <div class="card shadow">
            <div class="card-header">
                <div class="d-md-none">
                    <select id="transaction-sections" class="form-control" wire:change="$set('tab', $event.target.value)">
                        <option value="descendants" {{ $tab === 'descendants' ? 'selected' : '' }}>Descendants</option>
                        <option value="json" {{ $tab === 'json' ? 'selected' : '' }}>JSON</option>
                    </select>
                </div>
                <div class="d-none d-md-block">
                    <ul class="nav nav-tabs-alt card-header-tabs">
                        <li class="nav-item">
                            <button class="btn nav-link {{ $tab === 'descendants' ? 'active' : '' }}" wire:click="$set('tab', 'descendants')">
                                <i class="bi bi-box opacity-70 me-2"></i> Descendants
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="btn nav-link {{ $tab === 'json' ? 'active' : '' }}" wire:click="$set('tab', 'json')">
                                <i class="bi bi-code-slash opacity-70 me-2"></i> JSON
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
                            <pre class="line-numbers"><code class="lang-json">{{ pretty_json($transaction->raw_json) }}</code></pre>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif
</div>
