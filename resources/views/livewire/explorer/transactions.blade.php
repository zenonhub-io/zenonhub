<div>
    <div class="card-header border-bottom">
        <div class="d-block d-sm-flex align-items-center">
            <div class="flex-grow-1">
                <h4 class="mb-0">
                    Transactions
                </h4>
                <div class="text-muted fs-sm mt-1 mb-3 mb-sm-0">
                    Showing the latest 50k results
                </div>
            </div>
            <div class="d-block d-md-flex justify-content-md-end">
                {{ ($data ? $data->links('vendor/livewire/top-links') : '') }}
            </div>
        </div>
    </div>
    <div class="w-100" wire:loading.delay>
        <div class="card-body">
            <div class="row">
                <div class="col-24 col-sm-8 offset-sm-8">
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
                        Hash
                    </th>
                    <th>
                        From
                    </th>
                    <th></th>
                    <th>
                        To
                    </th>
                    <th>
                        Type
                    </th>
                    <th>
                        Amount
                    </th>
                    <th>
                        Token
                    </th>
                    <th>
                        Timestamp
                    </th>
                </tr>
                </thead>
                <tbody>
                @foreach($data as $block)
                    <tr>
                        <td class="pe-0">
                            @if ($block->is_un_received)
                                <span data-bs-toggle="tooltip" data-bs-title="Unreceived">
                                    {!! svg('explorer.unreceived', 'text-danger') !!}
                                </span>
                            @endif
                        </td>
                        <td>
                            <a href=" {{ route('explorer.transaction', ['hash' => $block->hash]) }}">
                                <x-hash-tooltip :hash="$block->hash" :eitherSide="8" :alwaysShort="true"/>
                            </a>
                        </td>
                        <td>
                            <x-address :account="$block->account" :eitherSide="8" :alwaysShort="true"/>
                        </td>
                        <td class="px-0">
                            {!! svg('explorer.send', 'text-success', 'transform: rotate(90deg);') !!}
                        </td>
                        <td>
                            <x-address :account="$block->to_account" :eitherSide="8" :alwaysShort="true"/>
                        </td>
                        <td>{{ ($block->display_type ?: '-') }}</td>
                        <td>{{ ($block->display_amount ?: '-')  }}</td>
                        <td>
                            @if ($block->token && $block->amount > 0)
                                <a href=" {{ route('explorer.token', ['zts' => $block->token->token_standard]) }}">
                                    {{ $block->token->custom_label }}
                                </a>
                            @else
                                -
                            @endif
                        </td>
                        <td>{{ $block->created_at->format(config('zenon.short_date_format')) }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        <div class="m-4 mt-2">
            {{ $data->onEachSide(0)->links() }}
        </div>
    </div>
</div>
