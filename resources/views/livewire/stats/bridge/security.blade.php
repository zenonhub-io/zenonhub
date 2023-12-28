<div>
    <div class="bg-secondary shadow rounded-2 p-3 mb-4">
        <div class="d-block d-md-flex justify-content-md-evenly">
            <div class="text-start text-md-center mb-2 mb-md-0">
                <span class="d-inline d-md-block text-muted fs-sm">Admin Delay <span class="text-muted"><i class="bi-question-circle" data-bs-toggle="tooltip" data-bs-title="Delay upon which the new administrator or guardians will be active"></i></span></span>
                <span class="float-end float-md-none">
                    {{  number_format($adminDelay) }}
                </span>
            </div>
            <div class="text-start text-md-center mb-2 mb-md-0">
                <span class="d-inline d-md-block text-muted fs-sm">Soft Delay <span class="text-muted"><i class="bi-question-circle" data-bs-toggle="tooltip" data-bs-title="Delay upon which all other time challenges will expire"></i></span></span>
                <span class="float-end float-md-none">
                    {{  number_format($softDelay) }}
                </span>
            </div>

            <div class="text-start text-md-center mb-2 mb-md-0">
                <span class="d-inline d-md-block text-muted fs-sm">Admin <span class="text-muted"><i class="bi-question-circle" data-bs-toggle="tooltip" data-bs-title="The admin address allowed to issue commands to the bridge"></i></span></span>
                <span class="float-end float-md-none">
                    <x-address :account="$admin->account" :eitherSide="8" breakpoint="lg" :named="false"/>
                </span>
            </div>
        </div>
    </div>

    <h5 class="mb-2">Time Challenges</h5>
    <div class="card shadow overflow-hidden mb-4">
        <div class="table-responsive">
            <table class="table table-nowrap table-striped table-hover">
                <thead>
                <tr>
                    <th>
                        Name
                    </th>
                    <th>
                        Active
                    </th>
                    <th>
                        Start Height
                    </th>
                    <th>
                        End Height
                    </th>
                </tr>
                <tbody>
                @foreach($timeChallenges as $challenge)
                    <tr>
                        <td>
                            {{$challenge['name']}}
                        </td>
                        <td>
                            <span
                                class="legend-indicator bg-{{ ($challenge['isActive'] ? 'success' : 'danger') }}"
                                data-bs-toggle="tooltip"
                                data-bs-title="@if($challenge['isActive']) Expires in {{ now()->addSeconds($challenge['endsIn'] * 10)->diffForHumans(['parts' => 2], true) }} ({{  number_format($challenge['endsIn']) }} momentums) @else Challenge not active @endif"></span>
                        </td>
                        <td>
                            {{ number_format($challenge['startHeight']) }}
                        </td>
                        <td>
                            {{ number_format($challenge['endHeight']) }}
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <h5 class="mb-2">Guardians</h5>
    <div class="card shadow overflow-hidden">
        <div class="table-responsive">
            <table class="table table-nowrap table-striped table-hover">
                <thead>
                <tr>
                    <th>
                        Address
                    </th>
                    <th>
                        Last Active
                    </th>
                </tr>
                <tbody>
                @foreach($guardians as $guardian)
                    <tr>
                        <td>
                            <x-address :account="$guardian->account" :eitherSide="8" :named="false"/>
                        </td>
                        <td>
                            {{ $guardian->account->updated_at?->format(config('zenon.short_date_format')) }}
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
