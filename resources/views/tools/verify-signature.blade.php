<x-app-layout>

    <x-includes.header :title="__('Verify Signature')" :responsive-border="false" />

    <div class="container-fluid px-3 px-md-6">
        <p>
            Don't trust, verify. Fill in the form below to verify if a message and signature is valid and matches the supplied address. Alternatively use our <x-link :href="route('tools.api-playground', ['request' => 'Utilities.verifySignedMessage'])">API</x-link> to verify messages in your own app.
        </p>

        <hr>

        <livewire:tools.verify-signature />
    </div>

</x-app-layout>
