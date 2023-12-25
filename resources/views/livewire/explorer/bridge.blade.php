<div>
    <div class="card shadow">
        <div class="card-header">
            <div class="d-block d-sm-flex align-items-center">
                <div class="flex-grow-1">
                    <h4 class="mb-3">
                        Bridge
                    </h4>
                </div>
                <div class="d-block d-md-flex justify-content-md-end mb-3">
                    {{ ($data ? $data->links('vendor/livewire/top-links') : '') }}
                </div>
            </div>

            <div class="p-0 d-flex align-items-center">
                <div class="p-0 flex-grow-1">
                    <div class="d-md-none">
                        <select id="account-sections" class="form-select" wire:change="setTab($event.target.value)">
                            <option value="inbound" {{ $tab === 'inbound' ? 'selected' : '' }}>Inbound</option>
                            <option value="outbound" {{ $tab === 'outbound' ? 'selected' : '' }}>Outbound</option>
                        </select>
                    </div>
                    <div class="d-none d-md-block">
                        <ul class="nav nav-tabs-alt card-header-tabs">
                            <li class="nav-item">
                                <button class="btn nav-link {{ $tab === 'inbound' ? 'active' : '' }}" wire:click="setTab('inbound')">
                                    Inbound
                                </button>
                            </li>
                            <li class="nav-item">
                                <button class="btn nav-link {{ $tab === 'outbound' ? 'active' : '' }}" wire:click="setTab('outbound')">
                                    Outbound
                                </button>
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="dropdown mb-n1 ms-3">
                    <i class="bi bi-funnel" style="font-size: 1.03rem" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false"></i>
                    <div class="dropdown-menu" style="min-width: 20rem">
                        <div class="px-4 py-3">
                            <h5>Tokens</h5>
                            @foreach($tokens as $token)
                                <div class="form-check mb-3">
                                    <input
                                        type="checkbox"
                                        class="form-check-input"
                                        id="tokens-{{ $token->symbol }}"
                                        name="tokens[]"
                                        wire:model.defer="filters.tokens"
                                        value="{{$token->symbol}}"
                                        {{ in_array($token->symbol, $filters['tokens']) ? 'checked' : ''}}>
                                    <label class="form-check-label" for="tokens-{{  $token->symbol }}">
                                        {{$token->name}}
                                    </label>
                                </div>
                            @endforeach
                            <div class="form-group mb-0">
                                <button wire:click="applyFilters" class="btn btn-sm btn-outline-secondary w-100">Apply</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="w-100" wire:loading>
            <div class="m-4">
                <div class="row">
                    <div class="col-24 col-md-8 offset-md-8">
                        <x-alert
                            message="Processing request..."
                            type="info"
                            icon="arrow-repeat spin"
                            class="d-flex justify-content-center mb-0"
                        />
                    </div>
                </div>
            </div>
        </div>
        <div wire:loading.remove>
            <div class="table-responsive">
                <table class="table table-nowrap align-middle table-striped table-hover">
                    <thead>
                    <tr>
                        <th style="min-width:32px"></th>
                        <th>
                            From
                        </th>
                        <th>
                            To
                        </th>
                        <th>
                            <button type="button" class="btn btn-sort" wire:click="sortBy('amount')">
                                <x-table-sort-button :sort="$sort" :order="$order" check="amount"/>
                            </button>
                        </th>
                        <th>
                            Token
                        </th>
                        <th>
                            Hash
                        </th>
                        <th>
                            Network
                        </th>
                        <th>
                            <button type="button" class="btn btn-sort" wire:click="sortBy('created_at')">
                                <x-table-sort-button :sort="$sort" :order="$order" check="created_at" title="Created"/>
                            </button>
                        </th>
                    </tr>
                    </thead>
                    <tbody>

                    @if($tab === 'inbound')
                        @foreach($data as $unwrap)
                            <tr>
                                <td class="pe-0">
                                    @if (! $unwrap->redeemed_at)
                                        <span data-bs-toggle="tooltip" data-bs-title="Unredeemed">
                                            {!! svg('explorer/unreceived', 'text-danger') !!}
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{$unwrap->from_address_link}}" target="_blank">
                                        <x-hash-tooltip :hash="$unwrap->from_address" :eitherSide="8" :alwaysShort="true"/>
                                    </a>
                                </td>
                                <td>
                                    <x-address :account="$unwrap->to_account" :eitherSide="8" :alwaysShort="true"/>
                                </td>
                                <td>
                                    {{ $unwrap->display_amount }}
                                </td>
                                <td>
                                    <a href="{{ route('explorer.token', ['zts' => $unwrap->token->token_standard]) }}">
                                        {{ $unwrap->token->custom_label }}
                                    </a>
                                </td>
                                <td>
                                    <a href="{{ $unwrap->tx_hash_link }}" target="_blank">
                                        <x-hash-tooltip hash="0x{{ $unwrap->transaction_hash}}" :eitherSide="8" :alwaysShort="true"/>
                                    </a>
                                </td>
                                <td>
                                    {{ $unwrap->bridge_network->name }}
                                </td>
                                <td>{{ $unwrap->created_at->format(config('zenon.short_date_format')) }}</td>
                            </tr>
                        @endforeach
                    @endif

                    @if($tab === 'outbound')
                        @foreach($data as $wrap)
                            <tr>
                                <td class="pe-0"></td>
                                <td>
                                    <x-address :account="$wrap->account" :eitherSide="8" :alwaysShort="true"/>
                                </td>
                                <td>
                                    <a href="{{$wrap->to_address_link}}" target="_blank">
                                        <x-hash-tooltip :hash="$wrap->to_address" :eitherSide="8" :alwaysShort="true"/>
                                    </a>
                                </td>
                                <td>
                                    {{ $wrap->display_amount }}
                                </td>
                                <td>
                                    <a href="{{ route('explorer.token', ['zts' => $wrap->token->token_standard]) }}">
                                        {{ $wrap->token->custom_label }}
                                    </a>
                                </td>
                                <td>
                                    <a href="{{ route('explorer.transaction', ['hash' => $wrap->account_block->hash]) }}">
                                        <x-hash-tooltip :hash="$wrap->account_block->hash" :eitherSide="8" :alwaysShort="true"/>
                                    </a>
                                </td>
                                <td>
                                    {{ $wrap->bridge_network->name }}
                                </td>
                                <td>{{ $wrap->created_at->format(config('zenon.short_date_format')) }}</td>
                            </tr>
                        @endforeach
                    @endif

                    </tbody>
                </table>
            </div>
            <div class="m-4 mt-2">
                {{ $data->onEachSide(0)->links() }}
            </div>
        </div>
    </div>
</div>
