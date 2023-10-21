<link rel="stylesheet" href={{ mix('css/app.css') }}>
@livewireStyles

@if (app()->isProduction())
    <script async src="https://www.googletagmanager.com/gtag/js?id={{ config('zenon.google_analytics_id') }}"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());

        gtag('config', '{{ config('zenon.google_analytics_id') }}');
    </script>
@endif


