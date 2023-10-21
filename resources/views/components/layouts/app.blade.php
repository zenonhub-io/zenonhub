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
@stack('scripts')
</body>
</html>
