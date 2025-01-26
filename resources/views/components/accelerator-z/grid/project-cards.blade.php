<div class="container-fluid px-3 px-md-6">
    <div class="row g-5">
        @foreach($projects as $project)
            <div class="col-24 col-lg-12">
                <x-accelerator-z.grid.project-card :project="$project" />
            </div>
        @endforeach
    </div>
</div>
