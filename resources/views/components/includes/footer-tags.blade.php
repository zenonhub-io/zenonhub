@livewireScripts

@if(app()->isProduction())
    <script src="{{ mix('js/app.js') }}"></script>
@else
    <script src="{{ mix('js/app.debug.js') }}"></script>
@endif



