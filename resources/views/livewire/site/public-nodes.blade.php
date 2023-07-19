<div>
    <div class="card card-shadow">
        <div class="card-header">
            <livewire:utilities.tab-header activeTab="{{ $tab }}" :tabs="[
                'sync' => 'Sync info',
                'process' => 'Process info',
                'network' => 'Network info',
            ]" />
        </div>
        <div class="tab-content">
            <div class="tab-pane show active p-4">
                <pre class="line-numbers"><code class="lang-json">{{ pretty_json($data) }}</code></pre>
            </div>
        </div>
    </div>
</div>
