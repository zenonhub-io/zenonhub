@push('scripts')
    <script>
        (function() {
            const chart{{ $uuid }} = window.ZenonHub.plugins.Chart.renderChart(
                document.getElementById("chart-{{ $uuid }}"),
                '{{ $type }}',
                @json($labels),
                @json($dataset),
                @json($options)
            );
            Livewire.on('updateChart', data => {
                chart{{ $uuid }}.data = data;
                chart{{ $uuid }}.update();
            });
        })();
    </script>
@endpush

<div>
    <div class="w-100">
        <canvas id="chart-{{ $uuid }}"></canvas>
    </div>
</div>
