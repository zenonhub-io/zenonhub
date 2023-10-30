<x-layouts.app>
    <x-slot name="breadcrumbs">
        {{ Breadcrumbs::render($view ?? 'tools') }}
    </x-slot>
    <div class="container">
        <div class="row">
            @if (isset($view))
                <div class="col-lg-8">
                    <x-includes.sidebar :items="[
                        'Stats' => [
                            [
                                'route' => 'stats.bridge',
                                'title' => 'Bridge',
                                'svg' => 'bridge',
                                'style' => 'width: 18px',
                            ],
                            [
                                'route' => 'stats.nodes',
                                'title' => 'Public nodes',
                                'icon' => 'hdd-rack-fill'
                            ],
							[
                                'route' => 'stats.accelerator',
                                'title' => 'Accelerator Z',
                                'icon' => 'rocket-takeoff-fill',
                            ],
                        ]
                    ]"/>
                </div>
                <div class="col-lg-16">
                    <livewire:is :component="$view" />
                </div>
            @else
                <div class="my-0 my-md-4"></div>
                <div class="col-24 col-sm-12 col-md-8 offset-md-4 mb-4">
                    <div class="card card-hover h-100 shadow text-center">
                        <div class="card-body d-flex align-items-center justify-content-center">
                            <div class="d-block">
                                <a href="{{ route('stats.bridge') }}" class="stretched-link">
                                    <span class="d-block mb-2">
                                        {!! svg('stats/bridge', 'mb-2', 'height: 2.3rem') !!}
                                    </span>
                                    <h5>Bridge</h5>
                                </a>
                                <p class="mb-0">
                                    View the multichain bridge stats
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-24 col-sm-12 col-md-8 mb-4">
                    <div class="card card-hover h-100 shadow text-center">
                        <div class="card-body d-flex align-items-center justify-content-center">
                            <div class="d-block">
                                <a href="{{ route('stats.nodes') }}" class="stretched-link">
                                    <span class="d-block mb-2">
                                        {!! svg('stats/nodes', 'mb-2', 'height: 2.3rem') !!}
                                    </span>
                                    <h5>Public Nodes</h5>
                                </a>
                                <p class="mb-0">
                                    View the networks public node stats
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-24 col-sm-12 col-md-8 offset-md-8 mb-4">
                    <div class="card card-hover h-100 shadow text-center">
                        <div class="card-body d-flex align-items-center justify-content-center">
                            <div class="d-block">
                                <a href="{{ route('stats.accelerator') }}" class="stretched-link">
                                    <span class="d-block mb-2">
                                        {!! svg('stats/accelerator', 'mb-2', 'height: 2.3rem') !!}
                                    </span>
                                    <h5>Accelerator Z</h5>
                                </a>
                                <p class="mb-0">
                                    View the Accelerator Z contract stats
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-layouts.app>
