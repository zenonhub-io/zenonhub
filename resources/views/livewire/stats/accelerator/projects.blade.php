<div wire:init="loadProjectData">
    <div class="bg-secondary shadow rounded-2 mb-4 p-3">
        <div class="d-block d-md-flex justify-content-md-evenly">
            <div class="text-start text-md-center mb-2 mb-md-0">
                <span class="d-inline d-md-block text-muted">New</span>
                <span class="float-end float-md-none text-white opacity-80">{{ $projectData['data'][0] ?? 0 }}</span>
            </div>
            <div class="text-start text-md-center mb-2 mb-md-0">
                <span class="d-inline d-md-block text-muted">Accepted</span>
                <span class="float-end float-md-none text-primary">{{$projectData['data'][1] ?? 0 }}</span>
            </div>
            <div class="text-start text-md-center mb-2 mb-md-0">
                <span class="d-inline d-md-block text-muted">Completed</span>
                <span class="float-end float-md-none text-success">{{ $projectData['data'][2] ?? 0 }}</span>
            </div>
            <div class="text-start text-md-center">
                <span class="d-inline d-md-block text-muted">Rejected</span>
                <span class="float-end float-md-none text-danger">{{ $projectData['data'][3] ?? 0 }}</span>
            </div>
        </div>
    </div>
    <div id="chart-az-project-totals" class="mb-md-0"></div>
</div>
