<x-layouts.app pageTitle="{{ $meta['title'] }}" pageDescription="{{ $meta['description'] }}">
    <x-slot name="pageBreadcrumbs">
        {{ Breadcrumbs::render($data['component'] ?? 'tools') }}
    </x-slot>
    <div class="container">
        <div class="row">
            @if (isset($data['component']))
                <div class="col-lg-8">
                    <x-site.sidebar :items="[
                        'Stats' => [
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
                    <livewire:is :component="$data['component']" />
                </div>
            @else
                <div class="col-24 col-sm-12 col-md-8 offset-md-8 mb-4">
                    <div class="card h-100 shadow text-center">
                        <div class="card-body d-flex align-items-center justify-content-center">
                            <div class="d-block">
                                <a href="{{ route('stats.nodes') }}">
                                    <span class="d-block">
                                        {!! svg('tools.nodes', 'mb-2', 'height: 2.3rem') !!}
                                    </span>
                                    <h5>Nodes</h5>
                                </a>
                                <p class="mb-0">
                                    View the networks public node stats
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
{{--                <div class="col-24 col-sm-12 col-md-8 mb-4">--}}
{{--                    <div class="card h-100 shadow text-center">--}}
{{--                        <div class="card-body d-flex align-items-center justify-content-center">--}}
{{--                            <div class="d-block">--}}
{{--                                <a href="{{ route('stats.accelerator') }}">--}}
{{--                                    <span class="d-block">--}}
{{--                                        {!! svg('tools.broadcast', 'mb-2', 'height: 2.3rem') !!}--}}
{{--                                    </span>--}}
{{--                                    <h5>Accelerator Z</h5>--}}
{{--                                </a>--}}
{{--                                <p class="mb-0">--}}
{{--                                    View the Accelerator Z contract stats--}}
{{--                                </p>--}}
{{--                            </div>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                </div>--}}
            @endif
        </div>
    </div>
</x-layouts.app>
