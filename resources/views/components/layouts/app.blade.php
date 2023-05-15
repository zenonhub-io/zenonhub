<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark-mode">
    <head>
        <x-site.meta/>
        <title>{{ $pageTitle }}</title>
        <meta name="title" content="{{ $pageTitle }}">
        <meta name="description" content="{{ $pageDescription }}">
        <meta property="twitter:title" content="{{ $pageTitle }}">
        <meta property="twitter:description" content="{{ $pageDescription }}">
        <meta property="twitter:image" content="{{ (request()->routeIs('home') ? url()->to('img/meta-big.png') : url()->to('img/meta-small.png')) }}?v=1">
        <meta property="twitter:url" content="{{ url()->current() }}">
        <meta property="twitter:card" content="summary">
        <meta property="twitter:site" content="@zenonhub">
        <meta property="og:site_name" content="{{ config('app.name') }}" />
        <meta property="og:title" content="{{ $pageTitle }}">
        <meta property="og:description" content="{{ $pageDescription }}">
        <meta property="og:url" content="{{ url()->current() }}">
        <meta property="og:image" content="{{ (request()->routeIs('home') ? url()->to('img/meta-big.png') : url()->to('img/meta-small.png')) }}?v=1">
        <meta property="og:type" content="website">

        <x-site.head-tags/>
        <style>
            .page-loading {
                position: fixed;
                top: 0;
                right: 0;
                bottom: 0;
                left: 0;
                width: 100%;
                height: 100%;
                -webkit-transition: all .4s .2s ease-in-out;
                transition: all .4s .2s ease-in-out;
                background-color: #fff;
                opacity: 0;
                visibility: hidden;
                z-index: 9999;
            }
            .dark-mode .page-loading {
                background-color: #191818;
            }
            .page-loading.active {
                opacity: 1;
                visibility: visible;
            }
            .page-loading-inner {
                position: absolute;
                top: 50%;
                left: 0;
                width: 100%;
                text-align: center;
                -webkit-transform: translateY(-50%);
                transform: translateY(-50%);
                -webkit-transition: opacity .2s ease-in-out;
                transition: opacity .2s ease-in-out;
                opacity: 0;
            }
            .page-loading.active > .page-loading-inner {
                opacity: 1;
            }
            .page-loading-inner > span {
                display: block;
                font-size: 1rem;
                font-weight: normal;
                color: #9397ad;
            }
            .dark-mode .page-loading-inner > span {
                color: #fff;
                opacity: .6;
            }
            .page-spinner {
                display: inline-block;
                width: 2.75rem;
                height: 2.75rem;
                margin-bottom: .75rem;
                vertical-align: text-bottom;
                border: .15em solid #b4b7c9;
                border-right-color: transparent;
                border-radius: 50%;
                -webkit-animation: spinner .75s linear infinite;
                animation: spinner .75s linear infinite;
            }
            .dark-mode .page-spinner {
                border-color: rgba(255,255,255,.4);
                border-right-color: transparent;
            }
            @-webkit-keyframes spinner {
                100% {
                    -webkit-transform: rotate(360deg);
                    transform: rotate(360deg);
                }
            }
            @keyframes spinner {
                100% {
                    -webkit-transform: rotate(360deg);
                    transform: rotate(360deg);
                }
            }
        </style>
    </head>
    <body>
        <div class="page-loading active">
            <div class="page-loading-inner">
                <div class="page-spinner"></div><span>Loading...</span>
            </div>
        </div>
        <main class="page-wrapper">
            <x-layouts.app-header/>
            {{ $slot }}
        </main>
        <x-layouts.app-footer/>
        <x-site.footer-tags/>
        @stack('scripts')
    </body>
</html>
