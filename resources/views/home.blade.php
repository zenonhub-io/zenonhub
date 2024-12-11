<x-app-layout>
    <x-includes.header :title="__('Explore the Network of Momentum')" :responsive-border="false" />

    <div class="container-fluid px-3 px-md-6">
        <div class="row mb-6 gy-6">
            <div class="col-24 col-sm-12 col-xl-6">
                <livewire:tiles.znn-supply lazy />
            </div>
            <div class="col-24 col-sm-12 col-xl-6">
                <livewire:tiles.qsr-supply lazy />
            </div>
            <div class="col-24 col-sm-12 col-xl-6">
                <livewire:tiles.accounts-overview lazy />
            </div>
            <div class="col-24 col-sm-12 col-xl-6">
                <livewire:tiles.transactions-overview lazy />
            </div>
        </div>

        <div class="row mb-6 gy-6">
            <div class="col-24 col-lg-12">
                <livewire:tiles.transactions-daily lazy timeframe="30d" />
            </div>
            <div class="col-24 col-lg-12">
                <livewire:tiles.accounts-total lazy timeframe="30d" />
            </div>
        </div>

        <div class="row mb-6 gy-6">
            <div class="col-24 col-lg-8">
                <livewire:tiles.latest-projects lazy />
            </div>
        </div>
    </div>

</x-app-layout>
