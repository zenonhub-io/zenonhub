@livewireScripts

@if(app()->isProduction())
    @vite(['resources/js/app.js'])
@else
    @vite(['resources/js/app.debug.js'])
@endif
