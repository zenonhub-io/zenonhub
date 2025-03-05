<x-app-layout>

    <x-includes.header :title="__('Plasma Bot')" :responsive-border="false" />

    <div class="container-fluid px-3 px-md-6">
        <p>
            Use the tool below to fuse some QSR and generate plasma for an address of your choosing. The plasma is only temporary and will automatically be removed.
        </p>

        <hr>

        @if ($enabled)
            <livewire:tools.plasma-bot />
        @else
            <x-alerts.alert type="info">
                <i class="bi bi-info-circle-fill me-2"></i> The bot is currently disabled, please check back later
            </x-alerts.alert>
        @endif
    </div>

</x-app-layout>
