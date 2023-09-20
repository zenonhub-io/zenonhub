<x-layouts.app pageTitle="{{ $meta['title'] }}" pageDescription="{{ $meta['description'] }}">
    <x-slot name="pageBreadcrumbs">
        {{ Breadcrumbs::render('services') }}
    </x-slot>
    <div class="container">
        <div class="row">
            <div class="my-0 my-md-4"></div>
            <div class="col-24 col-sm-12 col-md-8 offset-md-4 mb-4">
                <div class="card card-hover h-100 shadow text-center">
                    <div class="card-body d-flex align-items-center justify-content-center">
                        <div class="d-block">
                            <a href="{{ route('services.public-nodes') }}" class="stretched-link">
                                <span class="d-block mb-2">
                                    {!! svg('services/public-nodes', 'mb-2', 'height: 2.3rem') !!}
                                </span>
                                <h5>Public Nodes</h5>
                            </a>
                            <p class="mb-0">
                                Secure access to the Zenon Network
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-24 col-sm-12 col-md-8 mb-4">
                <div class="card card-hover h-100 shadow text-center">
                    <div class="card-body d-flex align-items-center justify-content-center">
                        <div class="d-block">
                            <a href="{{ route('services.plasma-bot') }}" class="stretched-link">
                                <span class="d-block mb-2">
                                    {!! svg('services/plasma-bot', 'mb-2', 'height: 2.3rem') !!}
                                </span>
                                <h5>Plasma Bot</h5>
                            </a>
                            <p class="mb-0">
                                Temporarily fuse QSR to an address
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-24 col-sm-12 col-md-8 offset-md-4 mb-4">
                <div class="card card-hover h-100 shadow text-center">
                    <div class="card-body d-flex align-items-center justify-content-center">
                        <div class="d-block">
                            <a href="{{ route('services.whale-alerts') }}" class="stretched-link">
                                <span class="d-block mb-2">
                                    {!! svg('services/whale-alerts', 'mb-2', 'height: 2.3rem') !!}
                                </span>
                                <h5>Whale Alerts</h5>
                            </a>
                            <p class="mb-0">
                                Real time notifications for ZNN & QSR
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-24 col-sm-12 col-md-8 mb-4">
                <div class="card card-hover h-100 shadow text-center">
                    <div class="card-body d-flex align-items-center justify-content-center">
                        <div class="d-block">
                            <a href="{{ route('services.bridge-alerts') }}" class="stretched-link">
                                <span class="d-block mb-2">
                                    {!! svg('services/bridge-alerts', 'mb-2', 'height: 2.3rem') !!}
                                </span>
                                <h5>Bridge Alerts</h5>
                            </a>
                            <p class="mb-0">
                                Real time admin action notifications
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
