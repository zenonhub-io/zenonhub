<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-bs-theme="dark">
    <head>
        <x-includes.meta/>
        <x-includes.head-tags/>
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

        <livewire:components.modal/>
        <livewire:components.offcanvas/>
        <x-includes.footer-tags/>
        @stack('scripts')
    </body>
</html>
