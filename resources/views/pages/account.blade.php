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
            {{ Breadcrumbs::render("account") }}
        @endif
    </x-slot>

    <div class="container">
        <div class="row">
            @if (isset($data['component']))
                <div class="col-lg-8">
                    <x-layouts.app-sidebar :items="[
                        'Account' => [
                            [
                                'route' => 'account.details',
                                'title' => 'Details',
                                'icon' => 'person-fill',
                            ],
							[
                                'route' => 'account.notifications',
                                'title' => 'Notifications',
                                'icon' => 'bell-fill',
                            ],
                            [
                                'route' => 'account.addresses',
                                'title' => 'Addresses',
                                'icon' => 'link-45deg',
                            ],
                            [
                                'route' => 'account.security',
                                'title' => 'Security',
                                'icon' => 'shield-shaded',
                            ],
                            [
                                'route' => 'logout',
                                'title' => 'Logout',
                                'icon' => 'lock-fill',
                            ]
                        ]
                    ]"/>
                </div>
                <div class="col-lg-16">
                    <x-dynamic-component :component="$data['component']" :data="$data"/>
                </div>
            @else
                <div class="col-24 col-sm-12 col-md-8 offset-md-4 mb-4">
                    <div class="card h-100 shadow text-center">
                        <div class="card-body d-flex align-items-center justify-content-center">
                            <div class="d-block">
                                <a href="{{ route('account.details') }}">
                                    <span class="d-block">
                                        <i class="bi-person-fill opacity-70" style="font-size:2.3rem;"></i>
                                    </span>
                                    <h5>Details</h5>
                                </a>
                                <p class="mb-0">
                                    Mange your account details
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-24 col-sm-12 col-md-8 mb-4">
                    <div class="card h-100 shadow text-center">
                        <div class="card-body d-flex align-items-center justify-content-center">
                            <div class="d-block">
                                <a href="{{ route('account.notifications') }}">
                                    <span class="d-block">
                                        <i class="bi-bell-fill opacity-70" style="font-size:2.3rem;"></i>
                                    </span>
                                    <h5>Notifications</h5>
                                </a>
                                <p class="mb-0">
                                    Setup alerts and notifications
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-24 col-sm-12 col-md-8 offset-md-4 mb-4">
                    <div class="card h-100 shadow text-center">
                        <div class="card-body d-flex align-items-center justify-content-center">
                            <div class="d-block">
                                <a href="{{ route('account.addresses') }}">
                                    <span class="d-block">
                                        <i class="bi-link-45deg opacity-70" style="font-size:2.3rem;"></i>
                                    </span>
                                    <h5>Addresses</h5>
                                </a>
                                <p class="mb-0">
                                    Link your network addresses
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-24 col-sm-12 col-md-8 mb-4">
                    <div class="card h-100 shadow text-center">
                        <div class="card-body d-flex align-items-center justify-content-center">
                            <div class="d-block">
                                <a href="{{ route('account.security') }}">
                                    <span class="d-block">
                                        <i class="bi-bell-fill opacity-70" style="font-size:2.3rem;"></i>
                                    </span>
                                    <h5>Security</h5>
                                </a>
                                <p class="mb-0">
                                    Change your account password
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-layouts.app>
