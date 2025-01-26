<x-app-layout>

    <x-includes.header :title="__('Plasma Bot')" :responsive-border="false" />

    <div class="container-fluid px-3 px-md-6">
        <p>
            Use the tool below to fuse some QSR and generate plasma for an address of your choosing. The plasma is only temporary and will automatically be removed.
        </p>

        <hr>

        <livewire:tools.plasma-bot />
    </div>

</x-app-layout>
