<x-layouts.app>
    <x-slot name="breadcrumbs">
        @if (isset($view))
            @if ($view === 'explorer.momentum')
                {{ Breadcrumbs::render($view, $momentum) }}
            @elseif ($view === 'explorer.transaction')
                {{ Breadcrumbs::render($view, $transaction) }}
            @elseif ($view === 'explorer.account')
                {{ Breadcrumbs::render($view, $account) }}
            @elseif ($view === 'explorer.token')
                {{ Breadcrumbs::render($view, $token) }}
            @endif
        @endif
    </x-slot>

    <div class="container mb-4">
        <div class="row">
            <div class="col-24">
                <livewire:explorer.search key="{{now()}}" />
                @if (isset($view))
                    @if ($view === 'explorer.momentum')
                        <livewire:explorer.momentum :key="now()" :hash="$momentum->hash" />
                    @elseif ($view === 'explorer.transaction')
                        <livewire:explorer.transaction :key="now()" :hash="$transaction->hash" />
                    @elseif ($view === 'explorer.account')
                        <livewire:explorer.account :key="now()" :address="$account->address" />
                    @elseif ($view === 'explorer.token')
                        <livewire:explorer.token :key="now()" :zts="$token->token_standard" />
                    @endif
                @endif
            </div>
        </div>
    </div>
    @push('scripts')
        <script src="{{ mix('js/pages/explorer.js') }}"></script>
    @endpush
</x-layouts.app>
