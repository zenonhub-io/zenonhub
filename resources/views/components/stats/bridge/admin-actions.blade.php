<div class="container-fluid">
    <div class="row">
        @foreach($actions as $action)
            <div class="col-24">
                <x-stats.bridge.admin-action :action="$action" />
            </div>
            <hr class="mt-4">
        @endforeach
    </div>
</div>
