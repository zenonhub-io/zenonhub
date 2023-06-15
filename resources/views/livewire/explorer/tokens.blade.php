<div>
    <div class="card-header border-bottom">
        <div class="d-block d-sm-flex align-items-center">
            <div class="flex-grow-1">
                <h4 class="mb-3 mb-sm-0">
                    Tokens
                </h4>
            </div>
            <div class="d-block d-md-flex justify-content-md-end">
                {{ ($data ? $data->links('vendor/livewire/top-links') : '') }}
            </div>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table table-nowrap align-middle table-striped table-hover">
            <thead>
            <tr>
                <th>
                    <button type="button" class="btn btn-sort" wire:click="sortBy('name')">
                        <x-table-sort-button :sort="$sort" :order="$order" check="name"/>
                    </button>
                </th>
                <th>
                    <button type="button" class="btn btn-sort" wire:click="sortBy('holders_count')">
                        <x-table-sort-button :sort="$sort" :order="$order" check="holders_count" title="Holders"/>
                    </button>
                </th>
                <th>
                    <button type="button" class="btn btn-sort" wire:click="sortBy('total_supply')">
                        <x-table-sort-button :sort="$sort" :order="$order" check="total_supply"/>
                    </button>
                </th>
                <th>
                    <button type="button" class="btn btn-sort" wire:click="sortBy('token_standard')">
                        <x-table-sort-button :sort="$sort" :order="$order" check="token_standard" title="Token Standard"/>
                    </button>
                </th>
                <th>
                    <button type="button" class="btn btn-sort" wire:click="sortBy('created_at')">
                        <x-table-sort-button :sort="$sort" :order="$order" check="created_at" title="Created"/>
                    </button>
                </th>
            </tr>
            </thead>
            <tbody>
            @foreach($data as $token)
                <tr>
                    <td>
                        <a href=" {{ route('explorer.token', ['zts' => $token->token_standard]) }}">
                            {{ $token->name }}
                        </a>
                        <span class="fs-xs">
                            {{ $token->symbol }}
                        </span>
                    </td>
                    <td>{{ number_format($token->holders_count) }}</td>
                    <td>{{ $token->getDisplayAmount($token->total_supply) }}</td>
                    <td>{{ short_hash($token->token_standard, 8) }}</td>
                    <td>{{ ($token->created_at ? $token->created_at?->format(config('zenon.short_date_format')) : '-') }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    <div class="m-4 mt-2">
        {{ $data->onEachSide(0)->links() }}
    </div>
</div>
