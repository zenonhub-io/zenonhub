<x-app-layout>

    <x-includes.header :title="__('Public RPC Nodes')" class="mb-4">
        <x-navigation.header.responsive-nav :items="[
            __('Overview') => route('stats.public-nodes'),
            __('List') => route('stats.public-nodes', ['tab' => 'list']),
        ]" :active="$tab" />
    </x-includes.header>

    @if ($tab === 'overview')
        <div class="container-fluid px-3 px-md-6">
            <div class="row mb-6 gy-6">
                <div class="col-12 col-lg-6">
                    <x-cards.card>
                        <x-cards.body>
                            <x-stats.mini-stat
                                :title="__('Total')"
                                :stat="$nodes->count()"
                                :info="__('Total number of public RPC nodes discovered')"
                            />
                        </x-cards.body>
                    </x-cards.card>
                </div>
                <div class="col-12 col-lg-6">
                    <x-cards.card>
                        <x-cards.body>
                            <x-stats.mini-stat
                                :title="__('Countries')"
                                :stat="$nodes->unique('country')->count()"
                                :info="__('Total unique countries')"
                            />
                        </x-cards.body>
                    </x-cards.card>
                </div>
                <div class="col-12 col-lg-6">
                    <x-cards.card>
                        <x-cards.body>
                            <x-stats.mini-stat
                                :title="__('Cities')"
                                :stat="$nodes->unique('city')->count()"
                                :info="__('Total unique cities')"
                            />
                        </x-cards.body>
                    </x-cards.card>
                </div>
                <div class="col-12 col-lg-6">
                    <x-cards.card>
                        <x-cards.body>
                            <x-stats.mini-stat
                                :title="__('Networks')"
                                :stat="$nodes->unique('isp')->count()"
                                :info="__('Total unique networks')"
                            />
                        </x-cards.body>
                    </x-cards.card>
                </div>

            </div>
            <div class="row">
                <div class="col-24">
                    <x-cards.card>
                        <x-cards.body>
                            <div id="js-node-map" style="height: 400px" data-markers="{{ $mapMarkers->toJson() }}"></div>
                        </x-cards.body>
                    </x-cards.card>
                </div>
            </div>
            <div class="row">
                <div class="col-24">
                    <x-cards.card class="mt-6">
                        <x-cards.heading class="border-0">
                            {{ __('Top Cities') }}
                        </x-cards.heading>
                        <div class="table-responsive px-4">
                            <table class="table table-nowrap table-flush">
                                <thead class="table-dark">
                                <tr>
                                    <th scope="col" class="text-center ps-3">#</th>
                                    <th scope="col">{{ __('City') }}</th>
                                    <th scope="col">{{ __('Country') }}</th>
                                    <th scope="col">{{ __('Nodes') }}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($topCities as $index => $node)
                                    <tr>
                                        <td class="text-center ps-3">
                                            {{ $index + 1 }}
                                        </td>
                                        <td>
                                            @if($node->city)
                                                {{ $node->city }}
                                            @else
                                                {{ __('Unknown') }}
                                            @endif
                                        </td>
                                        <td>
                                            @if($node->country)
                                                <x-svg file="flags/{{ Str::lower($node->country_code) }}" style="height: 11px" class="me-2" /> {{ $node->country }}
                                            @else
                                                {{ __('Unknown') }}
                                            @endif
                                        </td>
                                        <td>
                                            {{ $node->count }}
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </x-cards.card>
                </div>
                <div class="col-24 col-md-12">
                    <x-cards.card class="mt-6">
                        <x-cards.heading class="border-0">
                            {{ __('Top Networks') }}
                        </x-cards.heading>
                        <div class="table-responsive px-4">
                            <table class="table table-nowrap table-flush">
                                <thead class="table-dark">
                                <tr>
                                    <th scope="col" class="text-center ps-3">#</th>
                                    <th scope="col">{{ __('Network') }}</th>
                                    <th scope="col">{{ __('Nodes') }}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($topNetworks as $index => $node)
                                    <tr>
                                        <td class="text-center ps-3">
                                            {{ $index + 1 }}
                                        </td>
                                        <td>
                                            {{ $node->isp }}
                                        </td>
                                        <td>
                                            {{ $node->count }}
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </x-cards.card>
                </div>
                <div class="col-24 col-md-12">
                    <x-cards.card class="mt-6">
                        <x-cards.heading class="border-0">
                            {{ __('Versions') }}
                        </x-cards.heading>
                        <div class="table-responsive px-4">
                            <table class="table table-nowrap table-flush">
                                <thead class="table-dark">
                                <tr>
                                    <th scope="col" class="text-center ps-3">#</th>
                                    <th scope="col">{{ __('Network') }}</th>
                                    <th scope="col">{{ __('Nodes') }}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($nodeVersions as $index => $node)
                                    <tr>
                                        <td class="text-center ps-3">
                                            {{ $index + 1 }}
                                        </td>
                                        <td>
                                            {{ $node->version }}
                                        </td>
                                        <td>
                                            {{ $node->count }}
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </x-cards.card>
                </div>
            </div>
        </div>
    @endif

    @if ($tab === 'list')
        <livewire:stats.nodes.node-list lazy />
    @endif

    @pushOnce('scripts')
        @vite(['resources/js/pages/nodeStats.js'])
    @endPushOnce

</x-app-layout>
