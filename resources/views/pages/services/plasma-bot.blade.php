<x-layouts.app pageTitle="{{ $meta['title'] }}" pageDescription="{{ $meta['description'] }}">
    <x-slot name="pageBreadcrumbs">
        {{ Breadcrumbs::render('services.plasma-bot') }}
    </x-slot>
    <div class="container">
        <div class="row">
            <div class="col-24 col-md-16 offset-md-4 mb-4">
                <div class="card shadow mb-4">
                    <div class="card-header text-center">
                        <h4 class="mb-0">Plasma Bot</h4>
                    </div>
                    <div class="card-body text-center">
                        Our plasma bot will temporarily fuse QSR to an address for a short amount of time enabling faster transactions, use one of the platforms below to get started.
                    </div>
                </div>
                <div class="row">
                    <div class="col-24 col-md-8">
                        <div class="card card-hover h-100 shadow text-center">
                            <div class="card-body d-flex align-items-center justify-content-center">
                                <div class="d-block">
                                    <a href="{{ route('tools.plasma-bot') }}" class="stretched-link">
                                        <span class="d-block mb-2">
                                            <i class="bi-globe2 opacity-70" style="font-size:2.3rem;"></i>
                                        </span>
                                        <h5>Web</h5>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-24 col-md-8">
                        <div class="card h-100 shadow text-center">
                            <div class="card-body d-flex align-items-center justify-content-center">
                                <div class="d-block">
{{--                                    <a href="#" class="stretched-link">--}}
                                        <span class="d-block mb-2">
                                            <i class="bi-discord opacity-70" style="font-size:2.3rem;"></i>
                                        </span>
                                        <h5>Discord<small class="text-muted fs-6 d-block">coming soon</small></h5>
{{--                                    </a>--}}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-24 col-md-8">
                        <div class="card h-100 shadow text-center">
                            <div class="card-body d-flex align-items-center justify-content-center">
                                <div class="d-block">
{{--                                    <a href="#" class="stretched-link">--}}
                                        <span class="d-block mb-2">
                                            <i class="bi-telegram opacity-70" style="font-size:2.3rem;"></i>
                                        </span>
                                        <h5>Telegram<small class="text-muted fs-6 d-block">coming soon</small></h5>
{{--                                    </a>--}}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
