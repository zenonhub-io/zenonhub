<div>
    <div class="card-header border-bottom">
        <div class="d-block d-sm-flex align-items-center">
            <div class="flex-grow-1">
                <h4 class="mb-3 mb-sm-0">
                    Transactions
                </h4>
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
                    <th>
                        Hash
                    </th>
                    <th>
                        Type
                    </th>
                    <th>
                        From
                    </th>
                    <th>
                        To
                    </th>
                    <th>
                        Method
                    </th>
                    <th>
                        Token
                    </th>
                    <th>
                        <button type="button" class="btn btn-sort" wire:click="sortBy('amount')">
                            <x-table-sort-button :sort="$sort" :order="$order" check="amount"/>
                        </button>
                    </th>
                    <th>
                        <button type="button" class="btn btn-sort" wire:click="sortBy('created_at')">
                            <x-table-sort-button :sort="$sort" :order="$order" check="created_at" title="Timestamp"/>
                        </button>
                    </th>
                </tr>
                </thead>
                <tbody>
                @foreach($data as $block)
                    <tr>
                        <td>
                            <a href=" {{ route('explorer.transaction', ['hash' => $block->hash]) }}">
                                <x-hash-tooltip :hash="$block->hash" :eitherSide="8" :alwaysShort="true"/>
                            </a>
                        </td>
                        <td>{{ $block->display_type }}</td>
                        <td>
                            <x-address :account="$block->account" :eitherSide="8" :alwaysShort="true"/>
                        </td>
                        <td>
                            <x-address :account="$block->to_account" :eitherSide="8" :alwaysShort="true"/>
                        </td>
                        <td>{{ ($block->contract_method ? $block->contract_method->name : '-')  }}</td>
                        <td>
                            @if ($block->token)
                                <a href=" {{ route('explorer.token', ['zts' => $block->token->token_standard]) }}">
                                    {{ $block->token->name }}
                                </a>
                            @else
                                -
                            @endif
                        </td>
                        <td>{{ ($block->list_display_amount ?: '-')  }}</td>
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
