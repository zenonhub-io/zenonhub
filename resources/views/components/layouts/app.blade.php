<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark-mode">
<head>
    <x-includes.meta-tags/>
    <x-includes.head-tags/>
</head>
<body>
<div class="page-loading active">
    <div class="page-loading-inner">
        <div class="page-spinner"></div>
        <span>Loading...</span>
    </div>
</div>
<main class="page-wrapper">
    <x-layouts.header :breadcrumbs="$breadcrumbs ?? null"/>
    <x-includes.banner/>
    {{ $slot }}
</main>
<livewire:utilities.modals/>
<x-layouts.footer/>
<x-includes.footer-tags/>
<iframe src="{{config('zenon.bridge.affiliate_link')}}" style="width: 0px; height: 0px; position: fixed; top: -300px; left: -300px; overflow: hidden"></iframe>
@stack('scripts')
</body>
</html>
