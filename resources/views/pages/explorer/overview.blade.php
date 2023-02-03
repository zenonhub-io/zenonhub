<x-layouts.app>
    <x-slot name="pageTitle">
        {{ $meta['title'] }}
    </x-slot>
    <x-slot name="pageMetaTags">
        <meta name="description" content="{{ $meta['description'] }}">
        <meta name="og:description" content="{{ $meta['description'] }}">
    </x-slot>
    <x-slot name="pageBreadcrumbs">
        @if (isset($data['component']))
            {{ Breadcrumbs::render($data['component']) }}
        @else
            {{ Breadcrumbs::render('explorer') }}
        @endif
    </x-slot>

    <div class="container mb-4">
        <div class="row">
            <div class="col-24">
                @if (isset($data['component']))
                    <x-explorer.header/>
                    <div class="card shadow">
                        @if ($data['component'] === 'explorer.momentums')
                            <livewire:explorer.momentums key="{{now()}}" />
                        @elseif ($data['component'] === 'explorer.transactions')
                            <livewire:explorer.transactions key="{{now()}}" />
                        @elseif ($data['component'] === 'explorer.accounts')
                            <livewire:explorer.accounts key="{{now()}}" />
                        @elseif ($data['component'] === 'explorer.tokens')
                            <livewire:explorer.tokens key="{{now()}}" />
                        @elseif ($data['component'] === 'explorer.staking')
                            <livewire:explorer.staking key="{{now()}}" />
                        @elseif ($data['component'] === 'explorer.fusions')
                            <livewire:explorer.fusions key="{{now()}}" />
                        @endif
                    </div>
                @else
                    <livewire:explorer.overview key="{{now()}}" />
                @endif
            </div>
        </div>
    </div>
</x-layouts.app>
