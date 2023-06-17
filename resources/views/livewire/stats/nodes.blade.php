<div>
    <div class="card shadow mb-4">
        <div class="card-header">
            <h4 class="mb-0">Public nodes</h4>
        </div>
        @if (empty($mapData))
            <div class="card-body">
                <x-alert
                    message="Unable to load node stats, please try again..."
                    type="info"
                    icon="info-circle-fill"
                    class="d-flex justify-content-center mb-0"
                />
            </div>
        @else
            <div class="card-header">
                <div class="d-md-none">
                    <select id="sections" class="form-control" wire:change="$emit('tabChange', $event.target.value)">
                        <option value="map" {{ $tab === 'map' ? 'selected' : '' }}>Map</option>
                        <option value="countries" {{ $tab === 'countries' ? 'selected' : '' }}>Countries</option>
                        <option value="cities" {{ $tab === 'cities' ? 'selected' : '' }}>Cities</option>
                        <option value="networks" {{ $tab === 'networks' ? 'selected' : '' }}>Networks</option>
                        <option value="versions" {{ $tab === 'versions' ? 'selected' : '' }}>Versions</option>
                    </select>
                </div>
                <div class="d-none d-md-block">
                    <ul class="nav nav-tabs-alt card-header-tabs">
                        <li class="nav-item">
                            <button class="btn nav-link {{ $tab === 'map' ? 'active' : '' }}" wire:click="$emit('tabChange', 'map')">
                                Map
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="btn nav-link {{ $tab === 'countries' ? 'active' : '' }}" wire:click="$emit('tabChange', 'countries')">
                                Countries
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="btn nav-link {{ $tab === 'cities' ? 'active' : '' }}" wire:click="$emit('tabChange', 'cities')">
                                Cities
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="btn nav-link {{ $tab === 'networks' ? 'active' : '' }}" wire:click="$emit('tabChange', 'networks')">
                                Networks
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="btn nav-link {{ $tab === 'versions' ? 'active' : '' }}" wire:click="$emit('tabChange', 'versions')">
                                Versions
                            </button>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="card-body">
                <div class="tab-content">
                    <div class="tab-pane show active">
                        @if ($tab === 'map')
                            <div class="bg-secondary shadow rounded-2 mb-3 p-3">
                                <div class="d-block d-md-flex justify-content-md-evenly">
                                    <div class="text-start text-md-center mb-2 mb-md-0">
                                        <span class="d-inline d-md-block text-muted">{{ Str::plural('Node', $nodes['total']) }}</span>
                                        <span class="float-end float-md-none">{{ $nodes['total'] }}</span>
                                    </div>
                                    <div class="text-start text-md-center mb-2 mb-md-0">
                                        <span class="d-inline d-md-block text-muted">{{ Str::plural('City', $nodes['cities']) }}</span>
                                        <span class="float-end float-md-none">{{ $nodes['cities'] }}</span>
                                    </div>
                                    <div class="text-start text-md-center">
                                        <span class="d-inline d-md-block text-muted">{{ Str::plural('Country', $nodes['countries']) }}</span>
                                        <span class="float-end float-md-none">{{ $nodes['countries'] }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="text-center">
                                <canvas
                                    id="cobe"
                                    style="width:min(80vmin, 800px);height:min(80vmin, 800px);"
                                ></canvas>
                            </div>
                        @elseif ($tab === 'countries')
                            <div id="chart-node-countries" class="mb-3"></div>
                        @elseif ($tab === 'cities')
                            <div id="chart-node-cities" class="mb-3"></div>
                        @elseif ($tab === 'networks')
                            <div id="chart-node-networks" class="mb-3"></div>
                        @elseif ($tab === 'versions')
                            <div id="chart-node-versions" class="mb-3"></div>
                        @endif
                        <div class="text-center text-md-end">
                            <span class="fs-sm text-muted">Updated: {{ $updated }} | Data provided by <a href="https://github.com/Sol-Sanctum/Zenon-PoCs/tree/main/znn_node_info">Sol Sanctum</a></span>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>

    @if (! empty($mapData))
        @push('scripts')
            <script src="{{ mix('js/pages/stats/nodes.js') }}"></script>
            <script>
                ((ZenonHub) => {
                    ZenonHub.addData('nodeMapCanvasId', 'cobe');
                    ZenonHub.addData('nodeMapMarkers', [@foreach ($mapData as $marker){ location: [{{$marker['lat']}}, {{$marker['lng']}}], size: {{($marker['count']*0.05)}} },@endforeach]);

                    ZenonHub.addData('nodeCountriesSeries', @json($countriesData['data']));
                    ZenonHub.addData('nodeCountriesLabels', @json($countriesData['labels']));

                    ZenonHub.addData('nodeCitiesSeries', @json($citiesData['data']));
                    ZenonHub.addData('nodeCitiesLabels', @json($citiesData['labels']));

                    ZenonHub.addData('nodeNetworkSeries', @json($networksData['data']));
                    ZenonHub.addData('nodeNetworkLabels', @json($networksData['labels']));

                    ZenonHub.addData('nodeVersionsSeries', @json($versionsData['data']));
                    ZenonHub.addData('nodeVersionsLabels', @json($versionsData['labels']));

                    ZenonHub.addData('initialTab', '{{ $tab }}');
                })(window.zenonHub);
            </script>
        @endpush
    @endif
</div>
