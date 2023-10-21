<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark-mode">
<head>
    {!! Meta::renderTags() !!}
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png?v=1">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png?v=1">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png?v=1">
    <link rel="manifest" href="/site.webmanifest?v=1">
    <link rel="mask-icon" href="/safari-pinned-tab.svg?v=1" color="#4277ff">
    <link rel="shortcut icon" href="/favicon.ico?v=1">
    <meta name="msapplication-TileColor" content="#da532c">
    <meta name="theme-color" content="#181819">

    <x-includes.head-tags/>
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
            border-color: rgba(255, 255, 255, .4);
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
        <div class="page-spinner"></div>
        <span>Loading...</span>
    </div>
</div>
<main class="page-wrapper">
    <x-layouts.app-header :pageBreadcrumbs="$pageBreadcrumbs ?? ''"/>
    <x-includes.banner/>
    {{ $slot }}
</main>
<livewire:utilities.modals/>
<x-layouts.app-footer/>
<x-includes.footer-tags/>
@stack('scripts')
</body>
</html>
