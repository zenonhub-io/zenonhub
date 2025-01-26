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

        <!-- GA Tracking -->
        @if (app()->isProduction())
            <script async src="https://www.googletagmanager.com/gtag/js?id={{ config('zenon-hub.google_analytics_id') }}"></script>
            <script>
                window.dataLayer = window.dataLayer || [];
                function gtag(){dataLayer.push(arguments);}
                gtag('js', new Date());

                gtag('config', '{{ config('zenon-hub.google_analytics_id') }}');
            </script>
        @endif
    </head>
    <body class="bg-body">

        <nav class="d-flex align-items-center navbar navbar-dark bg-transparent">
            <div class="container-fluid ps-3">
                <div class="navbar-brand d-flex align-items-center mx-auto">
                    <x-includes.navbar.brand />
                </div>
            </div>
        </nav>

        <div class="
            body-container col-md-12 mx-auto
            flex-fill bg-body-tertiary
            shadow-2
            rounded-0 rounded-md-4
            auth-content-wrapper
        ">
            <main class="container-fluid px-3 py-5 p-md-6 vh-100 h-md-auto">
                {{ $slot }}
            </main>
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
