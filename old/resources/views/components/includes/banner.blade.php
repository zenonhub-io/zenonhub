@if(config('banner.enabled'))
    <div class="container">
        <x-alert
            message="{!! config('banner.message') !!}"
            type="{{config('banner.class')}}"
            icon="{{config('banner.icon')}}"
            class="d-flex justify-content-center mb-4"
        />
    </div>
@endif
