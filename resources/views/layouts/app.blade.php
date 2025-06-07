<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-bs-theme="dark">
    <head>
        <x-includes.meta/>

        <!-- Fonts -->
        <link rel="preconnect" href="https://api.fontshare.com">
        <link rel="stylesheet" href="https://api.fontshare.com/v2/css?f=satoshi@900,700,500,300,400&display=swap">

        <!-- Scripts & Styles -->
        @vite(['resources/scss/app.scss', 'resources/scss/utility.scss', 'resources/js/app.js'])
        @livewireStyles
        @livewireScriptConfig
        @livewireChartsScripts
        @stack('styles')
        @stack('headTags')

        <!-- GA Tracking -->
        @if (config('zenon-hub.google_analytics_id') && app()->isProduction())
            <script async src="https://www.googletagmanager.com/gtag/js?id={{ config('zenon-hub.google_analytics_id') }}"></script>
            <script>
                window.dataLayer = window.dataLayer || [];
                function gtag(){dataLayer.push(arguments);}
                gtag('js', new Date());

                gtag('config', '{{ config('zenon-hub.google_analytics_id') }}');
            </script>
        @endif
    </head>
    <body class="bg-dark">
        <header class="app-navbar navbar sticky-top p-1 justify-content-between" data-bs-theme="dark">
            <div class="navbar-brand ps-3 d-flex align-items-center">
                <x-includes.navbar.brand />
            </div>
            <div class="d-md-flex d-none align-items-center gap-6">
                <x-includes.navbar.stats />
            </div>
            <div class="d-flex align-items-center gap-2 pe-1">
                <button class="btn d-flex align-items-center py-1 px-3 rounded-pill bg-body-secondary-hover border-0 border-lg-1 border-gray-700" type="button"
                        x-data
                        x-on:click="$dispatch('open-livewire-modal', { alias: 'site-search', params: {}, static: false, keyboard: false, size: 'modal-lg'})">
                    <i class="bi bi-search fs-3"></i>
                </button>
                <div class="d-md-flex d-none">
                    <x-includes.navbar.user />
                </div>
                <div class="dropdown">
                    <span class="btn btn-outline-primary btn-xs dropdown-toggle me-0 me-md-2" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        {{ __(is_hqz() ? 'HQZ' : 'NoM') }}
                    </span>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="{{ config('zenon-hub.nom_explorer_url') }}">{{ __('NoM') }}</a></li>
                        <li><a class="dropdown-item" href="{{ config('zenon-hub.hqz_explorer_url') }}">{{ __('HQZ') }}</a></li>
                    </ul>
                </div>
                <button class="btn d-flex align-items-center py-1 px-3 rounded-pill bg-body-secondary-hover border-0 border-lg-1 border-gray-700 d-md-none" type="button"
                        data-bs-toggle="offcanvas" data-bs-target="#sidebarMenu"
                        aria-controls="sidebarMenu" aria-expanded="false" aria-label="Toggle navigation"
                >
                    <i class="bi bi-list fs-3"></i>
                </button>
            </div>
        </header>

        <div class="app-wrapper">
            <div class="offcanvas-md offcanvas-end" tabindex="-1" id="sidebarMenu" aria-labelledby="sidebarMenuLabel">
                <div class="app-sidebar-wrapper">
                    <div class="offcanvas-header">
                        <x-includes.navbar.brand />
                        <button type="button" class="btn-close ms-auto" data-bs-dismiss="offcanvas" data-bs-target="#sidebarMenu" aria-label="Close"></button>
                    </div>
                    <div class="offcanvas-body d-flex align-items-start flex-column">
                        <nav class="navbar navbar-vertical navbar-expand navbar-dark">
                            <x-includes.sidebar.nav />
                        </nav>
                        <div class="mt-auto w-100 pt-6 pt-md-4 border-top">
                            @if(is_hqz())
                                <x-cards.card class="mx-4">
                                    <x-cards.body class="pt-3">
                                        <div class="vstack text-center">
                                            <img src="{{ asset('build/img/adverts/hqz.png') }}" class="img-fluid mb-2 mx-auto" style="width: 100px" alt="Hyperqube_z Network">
                                            <p class="lead mb-4">
                                                {{ __('Hyperqube_z Extension Chain') }}
                                            </p>
                                            <x-link href="https://forum.hypercore.one/t/hyperqube-z-launch-instructions/583" class="btn btn-sm btn-outline-primary w-full">
                                                {{ __('Learn more') }} <i class="bi bi-arrow-right ms-2"></i>
                                            </x-link>
                                        </div>
                                    </x-cards.body>
                                </x-cards.card>
                            @else
                                <x-includes.sidebar.adverts />
                            @endif
                            <x-includes.sidebar.footer />
                        </div>
                    </div>
                </div>
            </div>

            <div class="app-content-wrapper bg-body-tertiary shadow-inset">
{{--                @if (isset($breadcrumbs))--}}
{{--                    <div class="border-bottom px-6 py-2">--}}
{{--                        {{ $breadcrumbs }}--}}
{{--                    </div>--}}
{{--                @endif--}}
                <main {{ $attributes->merge(['class' => 'mt-5 mb-3']) }}>
                    {{ $slot }}
                </main>
            </div>
        </div>

        @persist('modal')
            <livewire:components.modal/>
        @endpersist
        @persist('offcanvas')
            <livewire:components.offcanvas/>
        @endpersist

        @stack('scripts')

        <script>
            window.onload = function () {
                const timezone = Intl.DateTimeFormat().resolvedOptions().timeZone
                if (timezone !== '{{ session()->get('timezone', 'UTC') }}') {
                    axios.post('{{ route('timezone.update') }}', { timezone })
                }

                Livewire.hook('request', ({ uri, options, payload, respond, succeed, fail }) => {
                    fail(({ status, content, preventDefault }) => {
                        if (status === 419) {
                            preventDefault()
                        }
                    })
                })
            }
        </script>

    </body>
</html>
