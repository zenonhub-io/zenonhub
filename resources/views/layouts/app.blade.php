<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-bs-theme="dark">
    <head>
        <x-includes.meta/>
        <x-includes.head-tags/>
        @livewireStyles
        @rappasoftTableStyles
    </head>
    <body class="bg-body">
        <div class="d-flex flex-column flex-lg-row h-lg-100 gap-3">

            <!-- Search bar -->
            <div class="d-block d-lg-none px-3 pt-3">
                <x-includes.navbar.search />
            </div>

            <nav class="d-flex align-items-center navbar navbar-vertical navbar-expand-lg navbar-dark bg-transparent show vh-lg-100 p-0" id="sidebar">
                <div class="container-fluid ps-3">

                    <!-- Brand -->
                    <div class="navbar-brand d-flex align-items-center ms-0 ms-lg-auto p-0 py-lg-3 me-auto">
                        <x-includes.navbar.brand />
                    </div>

                    <!-- Mobile stats -->
                    <div class="d-none d-md-flex d-lg-none align-items-center gap-6 px-6 me-auto">
                        <x-includes.navbar.stats />
                    </div>

                    <!-- Mobile user menu -->
                    <div class="navbar-user d-lg-none me-3">
                        <x-includes.navbar.user />
                    </div>

                    <!-- Mobile nav toggle -->
                    <button class="navbar-toggler border-1 border-gray-600 shadow-6" type="button"
                            data-bs-toggle="collapse" data-bs-target="#sidebarCollapse"
                            aria-controls="sidebarCollapse" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>

                    <!-- Sidebar -->
                    <div class="collapse navbar-collapse overflow-x-hidden" id="sidebarCollapse">
                        <x-includes.sidebar.nav />
                        <div class="mt-auto"></div>
                        <x-includes.sidebar.sponsor />
                        <x-includes.sidebar.footer />
                    </div>

                </div>
            </nav>

            <div class="flex-lg-fill overflow-x-auto ps-lg-1 vstack vh-lg-100 position-relative">

                <!-- Top navbar -->
                <div class="d-none d-lg-flex py-3">

                    <!-- Search bar -->
                    <div class="flex-grow-1">
                        <x-includes.navbar.search />
                    </div>

                    <!-- Stats -->
                    <div class="d-md-none d-lg-flex align-items-center gap-6 px-6">
                        <x-includes.navbar.stats />
                    </div>

                    <!-- User menu -->
                    <div class="hstack justify-content-end ms-auto pe-6 text-nowrap">
                        <x-includes.navbar.user />
                    </div>
                </div>

                <!-- Main content -->
                <div class="app-content-wrapper flex-fill bg-body-tertiary overflow-y-lg-auto
                    rounded-top-0 rounded-top-start-lg-4 rounded-top-end-lg-0
                    shadow-inset
                    d-flex flex-column
                ">
                    <main class="container-fluid pt-5 pb-3 px-0 flex-grow-1">
                        {{ $slot }}
                    </main>
                </div>
            </div>
        </div>

        <livewire:components.modal/>
        <livewire:components.offcanvas/>
        <x-includes.footer-tags/>
        @stack('scripts')
        @rappasoftTableScripts

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
