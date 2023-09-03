<div wire:init="loadSecurityData">
    <h5 class="mb-2">Time Challenges</h5>
    <pre class="line-numbers mb-4"><code class="lang-json">{{ pretty_json($timeChallenges) }}</code></pre>

    <h5 class="mb-2">Guardians</h5>
    <pre class="line-numbers"><code class="lang-json">{{ pretty_json($guardians) }}</code></pre>
</div>
