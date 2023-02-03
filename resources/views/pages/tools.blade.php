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
            {{ Breadcrumbs::render("{$data['component']}") }}
        @else
            {{ Breadcrumbs::render("tools") }}
        @endif
    </x-slot>

    <div class="container">
        <div class="row">
            @if (isset($data['component']))
                <div class="col-lg-8">
                    <x-layouts.app-sidebar :items="[
                        'Tools' => [
                            [
                                'route' => 'tools.api-playground',
                                'title' => 'API Playground',
                                'icon' => 'cloud-fill',
                            ],
							[
                                'route' => 'tools.node-statistics',
                                'title' => 'Node statistics',
                                'icon' => 'hdd-stack-fill',
                            ],
                            [
                                'route' => 'tools.broadcast-message',
                                'title' => 'Broadcast message',
                                'icon' => 'send-fill',
                            ],
                            [
                                'route' => 'tools.verify-signature',
                                'title' => 'Verify signature',
                                'icon' => 'check2-circle',
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
                                    <i class="bi-cloud-fill opacity-70" style="font-size:2.3rem;"></i>
                                </span>
                                    <h5>Build</h5>
                                </a>
                                <p class="mb-0">
                                    Use our API in your own project.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-24 col-sm-12 col-md-8 mb-4">
                    <div class="card h-100 shadow text-center">
                        <div class="card-body d-flex align-items-center justify-content-center">
                            <div class="d-block">
                                <a href="{{ route('tools.node-statistics') }}">
                                    <span class="d-block">
                                        <i class="bi-hdd-stack-fill opacity-70" style="font-size:2.3rem;"></i>
                                    </span>
                                    <h5>Nodes</h5>
                                </a>
                                <p class="mb-0">
                                    View the networks node statistics.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-24 col-sm-12 col-md-8 offset-md-4 mb-4">
                    <div class="card h-100 shadow text-center">
                        <div class="card-body d-flex align-items-center justify-content-center">
                            <div class="d-block">
                                <a href="{{ route('tools.broadcast-message') }}">
                                    <span class="d-block">
                                        <i class="bi-send-fill opacity-70" style="font-size:2.3rem;"></i>
                                    </span>
                                    <h5>Broadcast</h5>
                                </a>
                                <p class="mb-0">
                                    Send a signed message to the forum.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-24 col-sm-12 col-md-8 mb-4">
                    <div class="card h-100 shadow text-center">
                        <div class="card-body d-flex align-items-center justify-content-center">
                            <div class="d-block">
                                <a href="{{ route('tools.verify-signature') }}">
                                    <span class="d-block">
                                        <i class="bi-check2-circle opacity-70" style="font-size:2.3rem;"></i>
                                    </span>
                                    <h5>Validate</h5>
                                </a>
                                <p class="mb-0">
                                    Confirm a message and signature match.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-layouts.app>
