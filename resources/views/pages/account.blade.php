<x-layouts.app pageTitle="{{ $meta['title'] }}" pageDescription="{{ $meta['description'] }}">
    <x-slot name="pageBreadcrumbs">
        {{ Breadcrumbs::render($data['component'] ?? 'account') }}
    </x-slot>
    <div class="container">
        <div class="row">
            @if (isset($data['component']))
                <div class="col-lg-8">
                    <x-site.sidebar :items="[
                        'Account' => [
                            [
                                'route' => 'account.details',
                                'title' => 'Details',
                            ],
							[
                                'route' => 'account.notifications',
                                'title' => 'Notifications',
                            ],
                            [
                                'route' => 'account.lists',
                                'title' => 'Lists',
                            ],
                            [
                                'route' => 'logout',
                                'title' => 'Logout',
                            ]
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
                                <a href="{{ route('account.details') }}">
                                    <span class="d-block">
                                        <i class="bi-person-circle opacity-70" style="font-size:2.3rem;"></i>
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
                                <a href="{{ route('account.list') }}">
                                    <span class="d-block">
                                        <i class="bi-shield-shaded opacity-70" style="font-size:2.3rem;"></i>
                                    </span>
                                    <h5>Lists</h5>
                                </a>
                                <p class="mb-0">
                                    Manage your custom list
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-24 col-sm-12 col-md-8 mb-4">
                    <div class="card h-100 shadow text-center">
                        <div class="card-body d-flex align-items-center justify-content-center">
                            <div class="d-block">
                                <a href="{{ route('logout') }}">
                                    <span class="d-block">
                                        <i class="bi-lock-fill opacity-70" style="font-size:2.3rem;"></i>
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
