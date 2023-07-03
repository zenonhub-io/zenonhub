<div wire:init="shouldLoadResults">
    <div class="p-4">
        <div class="row">
            <div class="col-24 col-md-16 mb-3 mb-md-0 align-self-center">
                <livewire:tables.toolbar :enableExport="true" :search="$search" />
            </div>
            <div class="col-24 col-md-8">
                <div class="d-flex justify-content-center justify-content-md-end">
                    {{ ($data ? $data->links('vendor/livewire/top-links') : '') }}
                </div>
            </div>
        </div>
    </div>
    <div class="w-100" wire:loading.delay>
        <div class="m-4 mt-0">
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
        @if ($data && $data->count())
            <div class="table-responsive">
                <table class="table table-nowrap align-middle table-striped table-hover top-border">
                    <thead>
                    <tr>
                        <th>
                            <button type="button" class="btn btn-sort" wire:click="sortBy('type')">
                                <x-table-sort-button :sort="$sort" :order="$order" check="type"/>
                            </button>
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
                    @foreach($data as $reward)
                        <tr>
                            <td>{{ $reward->display_type }}</td>
                            <td>
                                <a href=" {{ route('explorer.token', ['zts' => $reward->token->token_standard]) }}">
                                    {{ $reward->token->custom_label }}
                                </a>
                            </td>
                            <td>{{ $reward->display_amount }}</td>
                            <td>{{ $reward->created_at->format(config('zenon.short_date_format')) }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            <div class="m-4 mt-2">
                {{ $data->links() }}
            </div>
        @elseif($data)
            <div class="m-4 mt-0">
                <x-alert
                    message="No rewards found"
                    type="info"
                    icon="info-circle-fill"
                    class="mb-0"
                />
            </div>
        @endif
    </div>
</div>
