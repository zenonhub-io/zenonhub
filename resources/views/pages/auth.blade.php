<x-layouts.app pageTitle="{{ $meta['title'] }}" pageDescription="{{ $meta['description'] }}">
    <div class="container">
        <div class="row">
            <div class="col-24 col-md-16 offset-md-4 col-lg-12 offset-lg-6">
                <x-dynamic-component :component="$data['component']" :data="$data" />
            </div>
        </div>
    </div>
</x-layouts.app>
