<x-layouts.app pageTitle="{{ $meta['title'] }}" pageDescription="{{ $meta['description'] }}">
    <x-slot name="pageBreadcrumbs">
        {{ Breadcrumbs::render($data['component'] ?? 'tools') }}
    </x-slot>
    <div class="container">
        <div class="row">
            @if (isset($data['component']))
                <div class="col-lg-8">
                    <x-includes.sidebar :items="[
                        'Tools' => [
							[
                                'route' => 'tools.plasma-bot',
                                'title' => 'Plasma Bot',
                                'icon' => 'fire'
                            ],
                            [
                                'route' => 'tools.api-playground',
                                'title' => 'API Playground',
                                'icon' => 'cloud-fill'
                            ],
                            [
                                'route' => 'tools.broadcast-message',
                                'title' => 'Broadcast message',
                                'icon' => 'broadcast'
                            ],
                            [
                                'route' => 'tools.verify-signature',
                                'title' => 'Verify signature',
                                'icon' => 'check-lg'
                            ],
                        ]
                    ]"/>
                </div>
                <div class="col-lg-16">
                    <livewire:is :component="$data['component']" />
                </div>
            @else
                <div class="my-0 my-md-4"></div>
                <div class="col-24 col-sm-12 col-md-8 offset-md-4 mb-4">
                    <div class="card card-hover h-100 shadow text-center">
                        <div class="card-body d-flex align-items-center justify-content-center">
                            <div class="d-block">
                                <a href="{{ route('tools.plasma-bot') }}" class="stretched-link">
                                    <span class="d-block mb-2">
                                        {!! svg('tools/plasma-bot', 'mb-2', 'height: 2.3rem') !!}
                                    </span>
                                    <h5>Plasma Bot</h5>
                                </a>
                                <p class="mb-0">
                                    Generate plasma for an address
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-24 col-sm-12 col-md-8 mb-4">
                    <div class="card card-hover h-100 shadow text-center">
                        <div class="card-body d-flex align-items-center justify-content-center">
                            <div class="d-block">
                                <a href="{{ route('tools.api-playground') }}" class="stretched-link">
                                    <span class="d-block mb-2">
                                        {!! svg('tools/api-playground', 'mb-2', 'height: 2.3rem') !!}
                                    </span>
                                    <h5>API Playground</h5>
                                </a>
                                <p class="mb-0">
                                    Explore the networks RPC endpoints
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-24 col-sm-12 col-md-8 offset-md-4 mb-4">
                    <div class="card card-hover h-100 shadow text-center">
                        <div class="card-body d-flex align-items-center justify-content-center">
                            <div class="d-block">
                                <a href="{{ route('tools.broadcast-message') }}" class="stretched-link">
                                    <span class="d-block mb-2">
                                        {!! svg('tools/broadcast', 'mb-2', 'height: 2.3rem') !!}
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
                <div class="col-24 col-sm-12 col-md-8 mb-4">
                    <div class="card card-hover h-100 shadow text-center">
                        <div class="card-body d-flex align-items-center justify-content-center">
                            <div class="d-block">
                                <a href="{{ route('tools.verify-signature') }}" class="stretched-link">
                                    <span class="d-block mb-2">
                                        {!! svg('tools/validate', 'mb-2', 'height: 2.3rem') !!}
                                    </span>
                                    <h5>Validate</h5>
                                </a>
                                <p class="mb-0">
                                    Confirm a signed message and signature
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-layouts.app>
