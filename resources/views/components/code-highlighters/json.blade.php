@props(['code' => null])

<x-cards.card class="mx-3 mx-md-6">
    <x-cards.body>
        <pre class="line-numbers mb-0"><code class="lang-json">{{ json_encode($code, JSON_PRETTY_PRINT) }}</code></pre>
    </x-cards.body>
</x-cards.card>
