<div>

    <x-includes.header>
        <livewire:components.tabs :items="[
            __('Info') => 'info',
            __('Sync') => 'sync',
            __('Network') => 'network',
        ]" activeTab="info" />
    </x-includes.header>

    <div class="mx-3 mx-md-6">
        <x-code-highlighters.json :code="$data" />
    </div>

</div>
