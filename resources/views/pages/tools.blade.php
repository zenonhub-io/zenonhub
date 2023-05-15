<x-layouts.app pageTitle="{{ $meta['title'] }}" pageDescription="{{ $meta['description'] }}">
    <x-slot name="pageBreadcrumbs">
        {{ Breadcrumbs::render($data['component'] ?? 'tools') }}
    </x-slot>
    <div class="container">
        <div class="row">
            @if (isset($data['component']))
                <div class="col-lg-8">
                    <x-site.sidebar :items="[
                        'Tools' => [
                            [
                                'route' => 'tools.api-playground',
                                'title' => 'API Playground',
                            ],
                            [
                                'route' => 'tools.broadcast-message',
                                'title' => 'Broadcast message',
                            ],
                            [
                                'route' => 'tools.verify-signature',
                                'title' => 'Verify signature',
                            ],
                        ]
                    ]"/>
                </div>
                <div class="col-lg-16">
                    <livewire:is :component="$data['component']" />
                </div>
            @else
                <div class="col-24 col-sm-12 col-md-8 offset-md-4 mb-4">
                    <div class="card h-100 shadow text-center">
                        <div class="card-body d-flex align-items-center justify-content-center">
                            <div class="d-block">
                                <a href="{{ route('tools.api-playground') }}">
                                    <span class="d-block">
                                        {!! svg('tools.api', 'mb-2', 'height: 2.3rem') !!}
                                    </span>
                                    <h5>Build</h5>
                                </a>
                                <p class="mb-0">
                                    Use our API in your own project
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-24 col-sm-12 col-md-8 mb-4">
                    <div class="card h-100 shadow text-center">
                        <div class="card-body d-flex align-items-center justify-content-center">
                            <div class="d-block">
                                <a href="{{ route('tools.broadcast-message') }}">
                                    <span class="d-block">
                                        {!! svg('tools.broadcast', 'mb-2', 'height: 2.3rem') !!}
                                    </span>
                                    <h5>Broadcast</h5>
                                </a>
                                <p class="mb-0">
                                    Send a signed message to the forum
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-24 col-sm-12 col-md-8 mb-4 offset-sm-6 offset-md-8">
                    <div class="card h-100 shadow text-center">
                        <div class="card-body d-flex align-items-center justify-content-center">
                            <div class="d-block">
                                <a href="{{ route('tools.verify-signature') }}">
                                    <span class="d-block">
                                        {!! svg('tools.validate', 'mb-2', 'height: 2.3rem') !!}
                                    </span>
                                    <h5>Validate</h5>
                                </a>
                                <p class="mb-0">
                                    Check a message and signature match
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-layouts.app>
