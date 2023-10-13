<x-layouts.app pageTitle="{{ $meta['title'] }}" pageDescription="{{ $meta['description'] }}">
    <div class="container mb-4 js-scroll-to">
        <div class="row">
            <div class="col-24">
                <h1>Phases</h1>

                @foreach($phaseData as $data)
                    <div class="card shadow mb-4">
                        <div class="card-body p-0">
                            <div class="p-4">
                                Project: <strong>{{$data['phase']->project->name}}</strong><br>
                                Phase: <strong>{{$data['phase']->name}}</strong><br>
                                Hash: <strong>{{$data['phase']->hash}}</strong><br>
                                Progress: <strong>{{$data['phase']->quorum_stauts}}</strong><br>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-nowrap align-middle table-striped table-hover top-border">
                                    <thead>
                                    <tr>
                                        <th>
                                            Pillar
                                        </th>
                                        <th>
                                            AZ Engagement
                                        </th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($data['pillars'] as $pillar)
                                            <tr>
                                                <td>
                                                    {{$pillar->name}}
                                                </td>
                                                <td>
                                                    {{$pillar->az_engagement}}%
                                                </td>

                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @endforeach


                <h1 class="mt-5">Projects</h1>

                @foreach($projectData as $data)
                    <div class="card shadow mb-4">
                        <div class="card-body p-0">
                            <div class="p-4">
                                Project: <strong>{{$data['project']->name}}</strong><br>
                                Hash: <strong>{{$data['project']->hash}}</strong><br>
                                Progress: <strong>{{$data['project']->quorum_stauts}}</strong><br>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-nowrap align-middle table-striped table-hover top-border">
                                    <thead>
                                    <tr>
                                        <th>
                                            Pillar
                                        </th>
                                        <th>
                                            AZ Engagement
                                        </th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($data['pillars'] as $pillar)
                                        <tr>
                                            <td>
                                                {{$pillar->name}}
                                            </td>
                                            <td>
                                                {{$pillar->az_engagement}}%
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</x-layouts.app>
