<div>
    <div class="card shadow">
        <div class="card-header">
            <livewire:utilities.tab-header activeTab="{{ $tab }}" :tabs="[
                'process' => 'Process info',
                'sync' => 'Sync info',
                'network' => 'Network info',
            ]" />
        </div>
        <div class="tab-content">
            <div class="tab-pane show active p-4">
                <pre class="line-numbers"><code class="lang-json">{{ json_encode($data, JSON_PRETTY_PRINT) }}</code></pre>
            </div>
        </div>
    </div>
</div>
