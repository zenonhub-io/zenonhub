<x-layouts.app pageTitle="{{ $meta['title'] }}" pageDescription="{{ $meta['description'] }}">
    <x-slot name="pageBreadcrumbs">
        {{ Breadcrumbs::render($data['component'] ?? 'account') }}
    </x-slot>
    <div class="container">
        <div class="row">
            @if (isset($data['component']))
                <div class="col-lg-8">
                    <x-includes.sidebar :items="[
                        'Account' => [
                            [
                                'route' => 'account.details',
                                'title' => 'Details',
                                'icon' => 'person-fill'
                            ],
							[
                                'route' => 'account.favorites',
                                'title' => 'Favorites',
                                'icon' => 'star-fill'
                            ],
							[
                                'route' => 'account.notifications',
                                'title' => 'Notifications',
                                'icon' => 'bell-fill'
                            ],
//							[
//                                'route' => 'account.addresses',
//                                'title' => 'Addresses',
//                                'icon' => 'wallet2',
//                            ],
                            [
                                'route' => 'logout',
                                'title' => 'Logout',
                                'icon' => 'box-arrow-right'
                            ]
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
                                <a href="{{ route('account.details') }}" class="stretched-link">
                                    <span class="d-block mb-2">
                                        {!! svg('account/details', 'mb-2', 'height: 2.3rem') !!}
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
                    <div class="card card-hover h-100 shadow text-center">
                        <div class="card-body d-flex align-items-center justify-content-center">
                            <div class="d-block">
                                <a href="{{ route('account.favorites') }}" class="stretched-link">
                                    <span class="d-block mb-2">
                                        {!! svg('account/favourites', 'mb-2', 'height: 2.3rem') !!}
                                    </span>
                                    <h5>Favorites</h5>
                                </a>
                                <p class="mb-0">
                                    Manage your custom list
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-24 col-sm-12 col-md-8 offset-md-4 mb-4">
                    <div class="card card-hover h-100 shadow text-center">
                        <div class="card-body d-flex align-items-center justify-content-center">
                            <div class="d-block">
                                <a href="{{ route('account.notifications') }}" class="stretched-link">
                                    <span class="d-block mb-2">
                                        {!! svg('account/notifications', 'mb-2', 'height: 2.3rem') !!}
                                    </span>
                                    <h5>Notifications</h5>
                                </a>
                                <p class="mb-0">
                                    Manage your notification subscriptions
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-24 col-sm-12 col-md-8 mb-4">
                    <div class="card card-hover h-100 shadow text-center">
                        <div class="card-body d-flex align-items-center justify-content-center">
                            <div class="d-block">
                                <a href="{{ route('logout') }}" class="stretched-link">
                                    <span class="d-block mb-2">
                                        <i class="bi-box-arrow-right opacity-70" style="font-size:2.3rem;"></i>
                                    </span>
                                    <h5>Logout</h5>
                                </a>
                                <p class="mb-0">
                                    Sign out of your account
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-layouts.app>
