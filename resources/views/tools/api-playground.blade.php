<x-app-layout>

    <x-includes.header :title="__('API Playground')" class="mb-4" />

    <div class="container-fluid px-3 px-md-6">
        <p>
            Use the form to query the Network of Momentum. We provide http endpoints to all the requests listed below, test your requests here and use them in your own project.
        </p>

        <hr>

        <livewire:tools.api-playground />
    </div>

</x-app-layout>
