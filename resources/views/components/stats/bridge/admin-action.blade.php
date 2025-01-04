@props(['action'])

<x-link :href="route('explorer.transaction.detail', ['hash' => $action->hash])" class="list-group-item p-4">
    <div class="d-block d-md-flex w-100 justify-content-md-between mb-2">
        <h4 class="mb-1 mb-md-0">{{ ($action->display_type ?: '-') }} > {{ $action->contractMethod->contract->name }}</h4>
        <span class="text-sm text-muted"><x-date-time.carbon :date="$action->created_at" /></span>
    </div>
    <div class="bg-dark-subtle p-4 rounded-2 border shadow-inset">
        <pre class="line-numbers mb-0"><code class="lang-json">{{ json_encode($action->data->decoded, JSON_PRETTY_PRINT) }}</code></pre>
    </div>
</x-link>
