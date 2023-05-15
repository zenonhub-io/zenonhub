<x-layouts.app pageTitle="{{ $meta['title'] }}" pageDescription="{{ $meta['description'] }}">
    <x-slot name="pageBreadcrumbs">
        @if (isset($data['component']))
            @if ($data['component'] === 'explorer.momentum')
                {{ Breadcrumbs::render($data['component'], $data['momentum']) }}
            @elseif ($data['component'] === 'explorer.transaction')
                {{ Breadcrumbs::render($data['component'], $data['transaction']) }}
            @elseif ($data['component'] === 'explorer.account')
                {{ Breadcrumbs::render($data['component'], $data['account']) }}
            @elseif ($data['component'] === 'explorer.token')
                {{ Breadcrumbs::render($data['component'], $data['token']) }}
            @endif
        @endif
    </x-slot>

    <div class="container mb-4">
        <div class="row">
            <div class="col-24">
                <x-explorer.header/>
                @if (isset($data['component']))
                    @if ($data['component'] === 'explorer.momentum')
                        <livewire:explorer.momentum :key="now()" :hash="$data['momentum']->hash" />
                    @elseif ($data['component'] === 'explorer.transaction')
                        <livewire:explorer.transaction :key="now()" :hash="$data['transaction']->hash" />
                    @elseif ($data['component'] === 'explorer.account')
                        <livewire:explorer.account :key="now()" :address="$data['account']->address" />
                    @elseif ($data['component'] === 'explorer.token')
                        <livewire:explorer.token :key="now()" :zts="$data['token']->token_standard" />
                    @endif
                @endif
            </div>
        </div>
    </div>
    @push('scripts')
        <script src="{{ mix('js/pages/explorer.js') }}"></script>
    @endpush
</x-layouts.app>
